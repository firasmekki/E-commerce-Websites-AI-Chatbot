<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return back()->with('success', 'Votre avis a été enregistré avec succès !');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);
        
        $review->delete();

        return back()->with('success', 'L\'avis a été supprimé avec succès.');
    }
}
