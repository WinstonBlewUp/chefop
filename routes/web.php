<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $selectedWorkProjects = \App\Models\Project::where('is_selected_work', true)->with('media')->get();
    
    return view('home', compact('selectedWorkProjects'));
});

Route::get('/dashboard', function () {
    $menu = MenuLink::with(['page', 'category'])->get();
    
    // Pages disponibles (pas déjà dans le menu)
    $availablePages = Page::where('published', true)
        ->whereNull('project_id') // Uniquement les pages autonomes
        ->whereNotIn('id', $menu->pluck('page_id')->filter())
        ->get();
    
    // Catégories disponibles (pas déjà dans le menu)
    $availableCategories = Category::whereNotIn('id', $menu->pluck('category_id')->filter())->get();

    // Statistiques pour le dashboard
    $stats = [
        'total_projects' => \App\Models\Project::count(),
        'total_pages' => Page::count(),
        'published_pages' => Page::where('published', true)->count(),
        'total_media' => \App\Models\Media::count(),
        'recent_projects' => \App\Models\Project::latest()->take(3)->get(),
        'recent_pages' => Page::latest()->take(3)->get(),
        'recent_media' => \App\Models\Media::latest()->take(6)->get(),
    ];

    return view('dashboard', compact('menu', 'availablePages', 'availableCategories', 'stats'));
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('pages', PageController::class);
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::delete('/menu/{menuLink}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::resource('/projects', ProjectController::class);
    Route::post('/projects/{project}/publish-page', [ProjectController::class, 'publishPage'])->name('projects.publish-page');
    Route::post('/projects/store-without-category', [ProjectController::class, 'storeWithoutCategory'])->name('projects.store-without-category');
});

Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/categories/{slug}', function (string $slug) {
    $category = Category::where('name', $slug)->firstOrFail();
    
    // Récupérer tous les projets de cette catégorie avec leurs médias
    $projects = $category->projects()->with('media')->get();
    
    return view('categories.show', compact('category', 'projects'));
})->name('categories.show');

Route::get('/stills', function () {
    $stillsCategory = Category::where('slug', 'stills')->first();
    $stillsProjects = $stillsCategory ? $stillsCategory->projects()->with('media')->get() : collect();
    
    return view('stills', compact('stillsProjects'));
});

Route::get('/contact', function () {
    return view('pages.contact');
});

require __DIR__.'/auth.php';

