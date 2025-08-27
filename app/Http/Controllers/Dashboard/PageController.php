<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with(['project', 'category'])->latest()->get();
        $projects = Project::all();
        $categories = Category::all();

        return view('dashboard.pages.index', compact('pages', 'projects', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'project' => 'nullable|exists:projects,id',
            'published' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['project_id'] = $validated['project'] ?? null;
        unset($validated['project']);
        $validated['published'] = filter_var($validated['published'], FILTER_VALIDATE_BOOLEAN);

        try {
            Page::create($validated);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de la création de la page : ' . $e->getMessage());
            abort(500, 'Erreur création : ' . $e->getMessage());
        }

        return redirect()->route('dashboard.pages.index')->with('success', 'Page créée avec succès.');
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'project' => 'nullable|exists:projects,id',
            'published' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['project_id'] = $validated['project'] ?? null;
        unset($validated['project']);
        $validated['published'] = filter_var($validated['published'], FILTER_VALIDATE_BOOLEAN);

        $page->update($validated);

        return redirect()->route('dashboard.pages.index')->with('success', 'Page mise à jour avec succès.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('dashboard.pages.index')->with('success', 'Page supprimée avec succès.');
    }

    public function show(string $slug)
    {
        $page = Page::with('project.media')
                    ->where('slug', $slug)
                    ->where('published', true)
                    ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
