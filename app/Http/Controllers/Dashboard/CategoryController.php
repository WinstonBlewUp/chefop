<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('projects')->latest()->get();

        return view('dashboard.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('dashboard.categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('dashboard.categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Vérifier si des projets sont associés
        if ($category->projects()->count() > 0) {
            return redirect()->route('dashboard.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des projets.');
        }

        $category->delete();

        return redirect()->route('dashboard.categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}
