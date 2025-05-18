<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();

        $templates = [
            'default' => 'Page vide',
            'legal' => 'Mentions légales',
            'contact' => 'Page de contact',
        ];

        return Inertia::render('admin/pages/Index', [
            'pages' => $pages,
            'templates' => $templates,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'template' => 'nullable|string',
            'published' => 'boolean',
        ]);


        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['template'] = $validated['template'] ?: 'default';
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
            'template' => 'nullable|string',
            'published' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['template'] = $validated['template'] ?: 'default';
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
        $page = Page::where('slug', $slug)
                    ->where('published', true)
                    ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
