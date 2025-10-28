<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\Category;
use App\Models\Project;   
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $selectedWorkProjects = \App\Models\Project::where('is_selected_work', true)->with('media')->get();
    
    return view('home', compact('selectedWorkProjects'));
});

Route::get('/dashboard', function () {
    $menu = MenuLink::with(['page', 'category'])->orderBy('order')->get();
    
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
        'total_categories' => Category::count(),
        'recent_projects' => \App\Models\Project::latest()->take(3)->get(),
        'recent_pages' => Page::latest()->take(3)->get(),
        'recent_media' => \App\Models\Media::latest()->take(6)->get(),
        'recent_categories' => Category::withCount('projects')->latest()->take(3)->get(),
    ];

    return view('dashboard', compact('menu', 'availablePages', 'availableCategories', 'stats'));
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update'); 
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('pages', PageController::class);
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::post('/menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder');
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
    $stillsProject = Project::where('slug', 'stills')->where('is_locked', true)->with(['media', 'category'])->first();

    return view('stills', compact('stillsProject'));
});

Route::get('/contact', [PageController::class, 'showContact'])->name('contact.show');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/contact', [\App\Http\Controllers\Dashboard\PageController::class, 'editContact'])->name('contact.edit');
    Route::put('/contact', [\App\Http\Controllers\Dashboard\PageController::class, 'updateContact'])->name('contact.update');
});

// Route d’édition de la page Contact (admin)
Route::get('/dashboard/contact', [App\Http\Controllers\Dashboard\PageController::class, 'editContact'])
    ->name('dashboard.contact.edit')
    ->middleware('auth');

// Route de mise à jour de la page Contact (admin)
Route::put('/dashboard/contact', [App\Http\Controllers\Dashboard\PageController::class, 'updateContact'])
    ->name('dashboard.contact.update')
    ->middleware('auth');
