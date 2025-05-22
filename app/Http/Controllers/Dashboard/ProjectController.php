<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Media;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        $media = Media::latest()->get();
        $categories = Category::all(); 
        return view('dashboard.projects.index', compact('media', 'categories', 'projects'));;
    }

    public function create()
    {
        $media = Media::latest()->get();
        $categories = Category::all();
        $projects = Project::latest()->get();
        return view('dashboard.projects.create', compact('media', 'categories', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:projects,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'media' => 'array',
            'media.*' => 'exists:media,id',
        ]);

        /* dd($validated); */

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        if (!empty($validated['media'])) {
            $project->media()->attach($validated['media']);
        }

        return redirect()->route('dashboard')->with('success', 'Projet créé avec succès.');
    }

    public function edit(Project $project)
    {
        $media = Media::latest()->get();
        $categories = Category::all();
        $attachedMedia = $project->media()->pluck('media.id')->toArray();
        return view('dashboard.projects.edit', compact('project', 'media', 'attachedMedia', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:projects,slug,' . $project->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'media' => 'array',
            'media.*' => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        $project->media()->sync($validated['media'] ?? []);

        return redirect()->route('dashboard')->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Projet supprimé.');
    }
}
