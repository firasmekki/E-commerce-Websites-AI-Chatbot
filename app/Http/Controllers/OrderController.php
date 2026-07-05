<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        // AMÉLIORATION DE SÉCURITÉ : Récupérer uniquement les commandes de l'utilisateur connecté
        $orders = Order::where('user_id', $request->user()->id)
            ->with('orderItems.product')
            ->when($request->has('status'), fn($q) => $q->byStatus($request->status))
            ->orderBy('order_date', 'desc')
            ->get();
        
        return view('orders.index', compact('orders'));
    }
    
    public function show(Order $order): View
    {
        // AMÉLIORATION DE SÉCURITÉ : Vérifier que la commande appartient bien à l'utilisateur connecté
        abort_unless($order->user_id === auth()->id(), 403, 'Accès non autorisé à cette commande.');

        $order->load('user', 'orderItems.product.category');
        return view('orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        // AMÉLIORATION DE SÉCURITÉ : Vérifier la propriété de la commande
        abort_unless($order->user_id === auth()->id(), 403, 'Accès non autorisé.');

        // Règle métier : Seules les commandes "pending" peuvent être annulées par le client
        if ($order->status !== 'pending') {
            return back()->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        // Exécuter l'annulation et la restauration du stock sous transaction SQL
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        });

        return redirect()->route('orders.index')->with('success', 'Votre commande a été annulée avec succès, et les articles ont été remis en stock.');
    }

    public function invoice(Order $order): View
    {
        // AMÉLIORATION DE SÉCURITÉ : Vérifier que la commande appartient bien à l'utilisateur connecté
        abort_unless($order->user_id === auth()->id(), 403, 'Accès non autorisé.');

        $order->load('user', 'orderItems.product.category', 'coupon');
        return view('orders.invoice', compact('order'));
    }
}
