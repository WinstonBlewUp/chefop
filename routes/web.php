<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Models\MenuLink;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    $menu = MenuLink::with('page')->get();
    $availablePages = Page::where('published', true)
        ->whereNotIn('id', $menu->pluck('page_id'))
        ->get();

    return view('dashboard', compact('menu', 'availablePages'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('pages', PageController::class);
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::delete('/menu/{menuLink}', [MenuController::class, 'destroy'])->name('menu.destroy');
});

Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

require __DIR__.'/auth.php';
