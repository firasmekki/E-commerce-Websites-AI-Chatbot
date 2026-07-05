<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $total = $this->cartTotal($request);
        $coupon = $request->session()->get('coupon');
        $discount = 0.00;

        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $discount = $total * ($coupon['value'] / 100);
            } else {
                $discount = min($coupon['value'], $total);
            }
        }

        $grandTotal = max(0.00, $total - $discount);

        return view('cart.index', [
            'cartItems' => $this->cartItems($request),
            'total' => $total,
            'coupon' => $coupon,
            'discount' => $discount,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . max($product->stock, 1)],
        ]);

        if ($product->stock <= 0) {
            return back()->with('error', 'Ce produit est indisponible.');
        }

        $cart = $request->session()->get('cart', []);
        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;
        $newQuantity = min($currentQuantity + $validated['quantity'], $product->stock);

        $cart[$product->id] = ['quantity' => $newQuantity];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produit ajoute au panier.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . max($product->stock, 1)],
        ]);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $validated['quantity'];
            $request->session()->put('cart', $cart);
        }

        return back()->with('success', 'Panier mis a jour.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Produit retire du panier.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Code promo invalide, expiré ou inactif.');
        }

        $request->session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
        ]);

        return back()->with('success', 'Code promo appliqué avec succès.');
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $request->session()->forget('coupon');
        return back()->with('success', 'Code promo retiré.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cartItems = $this->cartItems($request);

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        $order = DB::transaction(function () use ($request, $cartItems) {
            $total = $cartItems->sum('subtotal');

            // Logique de coupon
            $coupon = $request->session()->get('coupon');
            $discount = 0.00;
            $couponId = null;

            if ($coupon) {
                $couponId = $coupon['id'];
                if ($coupon['type'] === 'percent') {
                    $discount = $total * ($coupon['value'] / 100);
                } else {
                    $discount = min($coupon['value'], $total);
                }
            }

            $grandTotal = max(0.00, $total - $discount);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $grandTotal,
                'discount_amount' => $discount,
                'coupon_id' => $couponId,
                'status' => 'pending',
                'order_date' => now(),
            ]);

            foreach ($cartItems as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']->effectivePrice(),
                    'subtotal' => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            $request->session()->forget('cart');
            $request->session()->forget('coupon');

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Commande creee avec succes.');
    }

    private function cartItems(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::with('category')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->map(function ($item, $productId) use ($products) {
                $product = $products->get((int) $productId);

                if (!$product) {
                    return null;
                }

                $quantity = min((int) $item['quantity'], max($product->stock, 1));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $quantity * $product->effectivePrice(),
                ];
            })
            ->filter()
            ->values();
    }

    private function cartTotal(Request $request): float
    {
        return $this->cartItems($request)->sum('subtotal');
    }
}
