<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Media;
use App\Models\Folder;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        $stillsProject = Project::where('slug', 'stills')->where('is_locked', true)->first();
        $regularProjects = Project::where('is_locked', false)->orWhereNull('is_locked')->latest()->get();

        // Charger uniquement les médias sans dossier
        $media = Media::whereNull('folder_id')->latest()->get();

        // Charger les dossiers racine avec leurs médias
        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();

        return view('dashboard.projects.index', compact('media', 'folders', 'categories', 'projects', 'stillsProject', 'regularProjects'));
    }

    public function create()
    {
        // Charger uniquement les médias sans dossier
        $media = Media::whereNull('folder_id')->latest()->get();

        // Charger les dossiers racine avec leurs médias
        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();
        $projects = Project::latest()->get();
        $stillsProject = Project::where('slug', 'stills')->where('is_locked', true)->first();
        $regularProjects = Project::where('is_locked', false)->orWhereNull('is_locked')->latest()->get();

        return view('dashboard.projects.create', compact('media', 'folders', 'categories', 'projects', 'stillsProject', 'regularProjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:projects,slug|unique:pages,slug',
            'description'       => 'nullable|string',
            'content'           => 'nullable|string',
            'category_id'       => 'nullable|exists:categories,id',
            'is_selected_work'  => 'nullable|boolean',
            'media'             => 'array',
            'media.*'           => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        // Redirection si aucune catégorie
        if (empty($validated['category_id'])) {
            return redirect()->route('dashboard.projects.create')->with([
                'show_category_modal' => true,
                'form_data' => $validated,
            ]);
        }

        $project = Project::create([
            'title'             => $validated['title'],
            'slug'              => $validated['slug'],
            'description'       => $validated['description'] ?? null,
            'content'           => $validated['content'] ?? null,
            'category_id'       => $validated['category_id'] ?? null,
            'is_selected_work'  => $request->boolean('is_selected_work'),
        ]);

        // Attacher les médias si présents
        if (!empty($validated['media'])) {
            $project->media()->sync($validated['media']);
        }

        // Créer la page associée
        Page::create([
            'title'      => $validated['title'],
            'slug'       => $validated['slug'],
            'content'    => $validated['content'] ?? '',
            'template'   => 'default',
            'published'  => false,
            'project_id' => $project->id,
        ]);

        return redirect()->route('dashboard.projects.create')->with([
            'success' => 'Projet et page associée créés avec succès.',
            'show_publish_modal' => $project->id
        ]);
    }

    public function storeWithoutCategory(Request $request)
    {
        $formData = $request->input('form_data', []);

        $project = Project::create([
            'title'             => $formData['title'],
            'slug'              => $formData['slug'],
            'description'       => $formData['description'] ?? null,
            'content'           => $formData['content'] ?? null,
            'category_id'       => null,
            'is_selected_work'  => !empty($formData['is_selected_work']),
        ]);

        if (!empty($formData['media'])) {
            $project->media()->sync($formData['media']);
        }

        Page::create([
            'title'      => $formData['title'],
            'slug'       => $formData['slug'],
            'content'    => $formData['content'] ?? '',
            'template'   => 'default',
            'published'  => false,
            'project_id' => $project->id,
        ]);

        return redirect()->route('dashboard.projects.create')->with([
            'success' => 'Projet créé sans catégorie.',
            'show_publish_modal' => $project->id
        ]);
    }

    public function edit(Project $project)
    {
        // Charger uniquement les médias sans dossier
        $media = Media::whereNull('folder_id')->latest()->get();

        // Charger les dossiers racine avec leurs médias
        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();
        $attachedMedia = $project->media()->pluck('media.id')->toArray();

        return view('dashboard.projects.edit', compact('project', 'media', 'folders', 'attachedMedia', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:projects,slug,' . $project->id . '|unique:pages,slug,' . optional($project->pages()->first())->id,
            'description'       => 'nullable|string',
            'content'           => 'nullable|string',
            'category_id'       => 'nullable|exists:categories,id',
            'is_selected_work'  => 'nullable|boolean',
            'media'             => 'array',
            'media.*'           => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project->update([
            'title'             => $validated['title'],
            'slug'              => $validated['slug'],
            'description'       => $validated['description'] ?? null,
            'content'           => $validated['content'] ?? null,
            'category_id'       => $validated['category_id'] ?? null,
            'is_selected_work'  => $request->boolean('is_selected_work'),
        ]);

        // Synchroniser les médias
        $project->media()->sync($validated['media'] ?? []);

        // Mise à jour de la page associée
        if ($page = $project->pages()->first()) {
            $page->update([
                'title'   => $validated['title'],
                'slug'    => $validated['slug'],
                'content' => $validated['content'] ?? '',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Projet et page associée mis à jour.');
    }

    public function destroy(Project $project)
    {
        if ($project->is_locked) {
            return redirect()->route('dashboard')->with('error', 'Ce projet ne peut pas être supprimé.');
        }

        $project->pages()->delete();
        $project->delete();

        return redirect()->route('dashboard')->with('success', 'Projet et pages associées supprimés.');
    }

    public function publishPage(Project $project)
    {
        if ($page = $project->pages()->first()) {
            $page->update(['published' => true]);
            return response()->json(['success' => true, 'message' => 'Page publiée avec succès.']);
        }

        return response()->json(['success' => false, 'message' => 'Page non trouvée.']);
    }
}
