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

        // Médias non organisés
        $media = Media::whereNull('folder_id')->latest()->get();

        // Dossiers racine avec leurs médias
        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();

        return view('dashboard.projects.index', compact(
            'media', 'folders', 'categories', 'projects', 'stillsProject', 'regularProjects'
        ));
    }

    public function create()
    {
        // Médias non organisés
        $media = Media::whereNull('folder_id')->latest()->get();

        // Dossiers racine avec leurs médias
        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();
        $projects = Project::latest()->get();
        $stillsProject = Project::where('slug', 'stills')->where('is_locked', true)->first();
        $regularProjects = Project::where('is_locked', false)->orWhereNull('is_locked')->latest()->get();

        return view('dashboard.projects.create', compact(
            'media', 'folders', 'categories', 'projects', 'stillsProject', 'regularProjects'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:projects,slug|unique:pages,slug',
            'description'       => 'nullable|string',
            'content'           => 'nullable|string',
            'categories'        => 'nullable|array',
            'categories.*'      => 'exists:categories,id',
            'is_selected_work'  => 'nullable|boolean',
            'media'             => 'nullable|array',
            'media.*'           => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project = Project::create([
            'title'            => $validated['title'],
            'slug'             => $validated['slug'],
            'description'      => $validated['description'] ?? null,
            'content'          => $validated['content'] ?? null,
            'is_selected_work' => $request->boolean('is_selected_work'),
        ]);

        // Sync médias
        if (!empty($validated['media'])) {
            $project->media()->sync($validated['media']);
        }

        // Sync catégories (many-to-many)
        $project->categories()->sync($validated['categories'] ?? []);

        // Créer la page associée
        Page::create([
            'title'      => $validated['title'],
            'slug'       => $validated['slug'],
            'content'    => $validated['content'] ?? '',
            'template'   => 'default',
            'published'  => false,
            'project_id' => $project->id,
        ]);

        // Rester sur la page (create) avec message + éventuel modal de publication
        return redirect()->back()->with([
            'success' => 'Projet et page associée créés avec succès.',
            'show_publish_modal' => $project->id,
        ])->withInput();
    }

    /**
     * Option "créer sans catégorie" – garde la compatibilité si tu l’utilises encore.
     * Attends form_data['categories'] en tableau (ou null).
     */
    public function storeWithoutCategory(Request $request)
    {
        $formData = $request->input('form_data', []);

        $project = Project::create([
            'title'            => $formData['title'],
            'slug'             => $formData['slug'],
            'description'      => $formData['description'] ?? null,
            'content'          => $formData['content'] ?? null,
            'is_selected_work' => !empty($formData['is_selected_work']),
        ]);

        // Médias
        if (!empty($formData['media'])) {
            $project->media()->sync($formData['media']);
        }

        // Catégories (many-to-many) — ici on force vide
        $project->categories()->sync($formData['categories'] ?? []);

        // Page associée
        Page::create([
            'title'      => $formData['title'],
            'slug'       => $formData['slug'],
            'content'    => $formData['content'] ?? '',
            'template'   => 'default',
            'published'  => false,
            'project_id' => $project->id,
        ]);

        return redirect()->back()->with([
            'success' => 'Projet créé sans catégorie.',
            'show_publish_modal' => $project->id,
        ])->withInput();
    }

    public function edit(Project $project)
    {
        $media = Media::whereNull('folder_id')->latest()->get();

        $folders = Folder::whereNull('parent_id')
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        $categories = Category::all();

        $attachedMedia = $project->media()->pluck('media.id')->toArray();
        $attachedCategories = $project->categories()->pluck('categories.id')->toArray();

        // Variables pour le sélecteur de thumbnail
        $currentThumb = $project->thumbnail_id
            ? ($project->relationLoaded('thumbnail') ? $project->thumbnail : Media::find($project->thumbnail_id))
            : null;
        $currentThumbFolderId = $currentThumb?->folder_id;

        return view('dashboard.projects.edit', compact(
            'project', 'media', 'folders', 'attachedMedia', 'categories', 'attachedCategories',
            'currentThumb', 'currentThumbFolderId'
        ));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:projects,slug,' . $project->id . '|unique:pages,slug,' . optional($project->pages()->first())->id,
            'description'       => 'nullable|string',
            'content'           => 'nullable|string',
            'categories'        => 'nullable|array',
            'categories.*'      => 'exists:categories,id',
            'is_selected_work'  => 'nullable|boolean',
            'media'             => 'array',
            'media.*'           => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project->update([
            'title'            => $validated['title'],
            'slug'             => $validated['slug'],
            'description'      => $validated['description'] ?? null,
            'content'          => $validated['content'] ?? null,
            'is_selected_work' => $request->boolean('is_selected_work'),
        ]);

        // Sync relations
        $project->media()->sync($validated['media'] ?? []);
        $project->categories()->sync($validated['categories'] ?? []);

        // MAJ page associée
        if ($page = $project->pages()->first()) {
            $page->update([
                'title'   => $validated['title'],
                'slug'    => $validated['slug'],
                'content' => $validated['content'] ?? '',
            ]);
        }

        // Rester sur la page d’édition
        return redirect()->back()->with('success', 'Projet et page associée mis à jour.')->withInput();
    }

    public function destroy(Project $project)
    {
        if ($project->is_locked) {
            return redirect()->back()->with('error', 'Ce projet ne peut pas être supprimé.');
        }

        $project->pages()->delete();
        $project->delete();

        // Retour à la page précédente si possible
        $previous = url()->previous();
        if ($previous && $previous !== url()->current()) {
            return redirect()->to($previous)->with('success', 'Projet et pages associées supprimés.');
        }
        // Fallback : index projets
        return redirect()->route('dashboard.projects.index')->with('success', 'Projet et pages associées supprimés.');
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
