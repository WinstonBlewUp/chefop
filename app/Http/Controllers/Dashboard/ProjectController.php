<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        return view('dashboard.projects.index', compact('projects'));
    }

    public function create()
    {
        $media = Media::latest()->get();
        return view('dashboard.projects.create', compact('media'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:projects,slug',
            'description' => 'nullable|string',
            'media' => 'array',
            'media.*' => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['media'])) {
            $project->media()->attach($validated['media']);
        }

        return redirect()->route('dashboard')->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        return view('dashboard.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        //
    }

    public function update(Request $request, Project $project)
    {
        //
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projet supprimé.');
    }
}
