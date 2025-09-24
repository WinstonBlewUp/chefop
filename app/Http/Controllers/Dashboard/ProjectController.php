<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Media;
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
        $media = Media::latest()->get();
        $categories = Category::all();

        return view('dashboard.projects.index', compact('media', 'categories', 'projects', 'stillsProject', 'regularProjects'));
    }

    public function create()
    {
        $media = Media::latest()->get();
        $categories = Category::all();
        $projects = Project::latest()->get();
        $stillsProject = Project::where('slug', 'stills')->where('is_locked', true)->first();
        $regularProjects = Project::where('is_locked', false)->orWhereNull('is_locked')->latest()->get();
        return view('dashboard.projects.create', compact('media', 'categories', 'projects', 'stillsProject', 'regularProjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|unique:projects,slug|unique:pages,slug',
            'description'         => 'nullable|string',
            'category_id'         => 'nullable|exists:categories,id',
            'is_selected_work'    => 'nullable|boolean',

            // Nouveaux champs
            'project_type'        => 'nullable|string|max:255',
            'director'            => 'nullable|string|max:255',
            'productors'          => 'nullable|string|max:255',
            'production_company'  => 'nullable|string|max:255',
            'distributor'         => 'nullable|string|max:255',
            'award'               => 'nullable|string|max:255',
            'misc'                => 'nullable|string|max:255',

            // Médias
            'media'               => 'array',
            'media.*'             => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        // Si aucune catégorie n'a été sélectionnée, on renvoie toutes les données au front (modale)
        if (empty($validated['category_id'])) {
            return redirect()->route('dashboard.projects.create')->with([
                'show_category_modal' => true,
                'form_data' => $validated,
            ]);
        }

        $project = Project::create([
            'title'              => $validated['title'],
            'slug'               => $validated['slug'],
            'description'        => $validated['description'] ?? null,
            'category_id'        => $validated['category_id'] ?? null,
            'is_selected_work'   => $request->boolean('is_selected_work'),

            // Nouveaux champs
            'project_type'       => $validated['project_type'] ?? null,
            'director'           => $validated['director'] ?? null,
            'productors'         => $validated['productors'] ?? null,
            'production_company' => $validated['production_company'] ?? null,
            'distributor'        => $validated['distributor'] ?? null,
            'award'              => $validated['award'] ?? null,
            'misc'               => $validated['misc'] ?? null,
        ]);

        if (!empty($validated['media'])) {
            $project->media()->attach($validated['media']);
        }

        // Créer automatiquement une page associée au projet
        Page::create([
            'title'      => $validated['title'],
            'slug'       => $validated['slug'],
            'content'    => $validated['description'] ?? '',
            'template'   => 'default',
            'published'  => false, // Par défaut non publié
            'project_id' => $project->id,
        ]);

        return redirect()->route('dashboard.projects.create')->with([
            'success' => 'Projet et page associée créés avec succès.',
            'show_publish_modal' => $project->id
        ]);
    }

    public function storeWithoutCategory(Request $request)
    {
        // Données renvoyées par la modale (issues du validated précédent)
        $formData = $request->input('form_data', []);

        $project = Project::create([
            'title'              => $formData['title'],
            'slug'               => $formData['slug'],
            'description'        => $formData['description'] ?? null,
            'category_id'        => null,
            'is_selected_work'   => !empty($formData['is_selected_work']),

            // Nouveaux champs
            'project_type'       => $formData['project_type'] ?? null,
            'director'           => $formData['director'] ?? null,
            'productors'         => $formData['productors'] ?? null,
            'production_company' => $formData['production_company'] ?? null,
            'distributor'        => $formData['distributor'] ?? null,
            'award'              => $formData['award'] ?? null,
            'misc'               => $formData['misc'] ?? null,
        ]);

        if (!empty($formData['media'])) {
            $project->media()->attach($formData['media']);
        }

        // Créer automatiquement une page associée au projet
        Page::create([
            'title'      => $formData['title'],
            'slug'       => $formData['slug'],
            'content'    => $formData['description'] ?? '',
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
        $media = Media::latest()->get();
        $categories = Category::all();
        $attachedMedia = $project->media()->pluck('media.id')->toArray();
        return view('dashboard.projects.edit', compact('project', 'media', 'attachedMedia', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|unique:projects,slug,' . $project->id . '|unique:pages,slug,' . optional($project->pages()->first())->id,
            'description'         => 'nullable|string',
            'category_id'         => 'nullable|exists:categories,id',
            'is_selected_work'    => 'nullable|boolean',

            // Nouveaux champs
            'project_type'        => 'nullable|string|max:255',
            'director'            => 'nullable|string|max:255',
            'productors'          => 'nullable|string|max:255',
            'production_company'  => 'nullable|string|max:255',
            'distributor'         => 'nullable|string|max:255',
            'award'               => 'nullable|string|max:255',
            'misc'                => 'nullable|string|max:255',

            'media'               => 'array',
            'media.*'             => 'exists:media,id',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $project->update([
            'title'              => $validated['title'],
            'slug'               => $validated['slug'],
            'description'        => $validated['description'] ?? null,
            'category_id'        => $validated['category_id'] ?? null,
            'is_selected_work'   => $request->boolean('is_selected_work'),

            // Nouveaux champs
            'project_type'       => $validated['project_type'] ?? null,
            'director'           => $validated['director'] ?? null,
            'productors'         => $validated['productors'] ?? null,
            'production_company' => $validated['production_company'] ?? null,
            'distributor'        => $validated['distributor'] ?? null,
            'award'              => $validated['award'] ?? null,
            'misc'               => $validated['misc'] ?? null,
        ]);

        $project->media()->sync($validated['media'] ?? []);

        // Mettre à jour la page associée si elle existe
        if ($associatedPage = $project->pages()->first()) {
            $associatedPage->update([
                'title'   => $validated['title'],
                'slug'    => $validated['slug'],
                'content' => $validated['description'] ?? '',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Projet et page associée mis à jour.');
    }

    public function destroy(Project $project)
    {
        // Empêcher la suppression des projets verrouillés
        if ($project->is_locked) {
            return redirect()->route('dashboard')->with('error', 'Ce projet ne peut pas être supprimé.');
        }

        // Supprimer les pages associées
        $project->pages()->delete();

        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Projet et pages associées supprimés.');
    }

    public function publishPage(Project $project)
    {
        $page = $project->pages()->first();

        if ($page) {
            $page->update(['published' => true]);
            return response()->json(['success' => true, 'message' => 'Page publiée avec succès.']);
        }

        return response()->json(['success' => false, 'message' => 'Page non trouvée.']);
    }
}
