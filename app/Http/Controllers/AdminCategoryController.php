<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        return redirect()->route('categories.index');
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        Category::create($this->validatedData($request));

        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function edit(Request $request, Category $category): View
    {
        $this->authorizeAdmin($request);

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $category->update($this->validatedData($request));

        return redirect()->route('categories.index')->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer une catégorie contenant des produits.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
