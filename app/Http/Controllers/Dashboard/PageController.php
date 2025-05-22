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
        $pages = Page::latest()->get();
        $projects = Project::all()->map(fn($project) => [
            'id' => $project->id,
            'title' => $project->title,
        ]);

        $categories = Category::with(['projects.media' => function ($query) {
            $query->orderBy('created_at')->limit(1);
        }])->get();

        return Inertia::render('admin/pages/Index', [
            'pages' => $pages,
            'projects' => $projects,
            'categories' => $categories,
        ]);
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

        return redirect()->route('dashboard.pages.index');
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

        return redirect()->route('dashboard.pages.index');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('dashboard.pages.index');
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
