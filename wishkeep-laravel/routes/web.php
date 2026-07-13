<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryRequestController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(Auth::check() ? '/dashboard' : '/login');
});

// ===== AUTH (guest only) =====
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// Alias GET /logout supaya konsisten dengan versi Node (link biasa, bukan tombol form)
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ===== WISHLIST (perlu login) =====
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WishlistController::class, 'dashboard'])->name('dashboard');

    Route::get('/wishlist/print', [WishlistController::class, 'print'])->name('wishlist.print');
    Route::get('/wishlist/export', [WishlistController::class, 'export'])->name('wishlist.export');
    Route::get('/wishlist/suggest', [WishlistController::class, 'suggest'])->name('wishlist.suggest');
    Route::get('/wishlist/catatan-belanja', [WishlistController::class, 'quickAddForm'])->name('wishlist.quick-add');
    Route::post('/wishlist/catatan-belanja', [WishlistController::class, 'quickAddStore'])->name('wishlist.quick-add.store');
    Route::get('/wishlist/new', [WishlistController::class, 'create'])->name('wishlist.create');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::get('/wishlist/{wishlist}/edit', [WishlistController::class, 'edit'])->name('wishlist.edit');
    Route::post('/wishlist/{wishlist}', [WishlistController::class, 'update'])->name('wishlist.update');
    Route::post('/wishlist/{wishlist}/delete', [WishlistController::class, 'destroy'])->name('wishlist.delete');
    Route::post('/wishlist/{wishlist}/toggle-status', [WishlistController::class, 'toggleStatus'])->name('wishlist.toggle');
    Route::post('/wishlist/{wishlist}/move-folder', [WishlistController::class, 'moveFolder'])->name('wishlist.move-folder');
    Route::post('/wishlist/{wishlist}/add-saving', [WishlistController::class, 'addSaving'])->name('wishlist.add-saving');

    Route::get('/folders', [FolderController::class, 'index'])->name('folders.index');
    Route::get('/folders/new', [FolderController::class, 'create'])->name('folders.create');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/folders/{folder}/edit', [FolderController::class, 'edit'])->name('folders.edit');
    Route::post('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::post('/folders/{folder}/delete', [FolderController::class, 'destroy'])->name('folders.delete');

    Route::get('/statistik', [StatsController::class, 'index'])->name('stats.index');

    Route::post('/categories/request', [CategoryRequestController::class, 'store'])->name('categories.request');
});

// ===== ADMIN (perlu login + role admin) =====
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users/{user}/delete', [UserController::class, 'destroy'])->name('admin.users.delete');

    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::post('/categories/{category}/approve', [CategoryController::class, 'approve'])->name('admin.categories.approve');
    Route::post('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('admin.categories.delete');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('admin.activity-log');
});
