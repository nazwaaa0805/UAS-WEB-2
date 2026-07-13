<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\WishlistFolder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $folders = $request->user()->wishlistFolders()
            ->withCount('items')
            ->with('items')
            ->orderBy('event_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($folder) {
                $folder->total_estimasi = $folder->items->where('status', 'belum')->sum('price');
                $folder->total_dibeli = $folder->items->where('status', 'dibeli')->count();
                return $folder;
            });

        // Item yang belum dimasukkan folder mana pun (opsional, ditampilkan sebagai info)
        $itemsWithoutFolder = $request->user()->wishlistItems()->whereNull('folder_id')->count();

        return view('folders.index', [
            'folders' => $folders,
            'itemsWithoutFolder' => $itemsWithoutFolder,
        ]);
    }

    public function create()
    {
        return view('folders.form', ['folder' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:10'],
            'event_date' => ['nullable', 'date'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
        ]);

        $folder = $request->user()->wishlistFolders()->create($validated);

        ActivityLog::log('folder', $request->user()->name . ' membuat folder "' . $folder->name . '"');

        return redirect()->route('folders.index')->with('success', 'Folder wishlist berhasil dibuat.');
    }

    public function edit(Request $request, WishlistFolder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            return redirect()->route('folders.index')->with('error', 'Folder tidak ditemukan.');
        }

        return view('folders.form', ['folder' => $folder]);
    }

    public function update(Request $request, WishlistFolder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            return redirect()->route('folders.index')->with('error', 'Folder tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:10'],
            'event_date' => ['nullable', 'date'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
        ]);

        $folder->update($validated);

        return redirect()->route('folders.index')->with('success', 'Folder berhasil diperbarui.');
    }

    public function destroy(Request $request, WishlistFolder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            return redirect()->route('folders.index')->with('error', 'Folder tidak ditemukan.');
        }

        // Item di dalam folder TIDAK ikut terhapus, cuma folder_id-nya jadi null
        // (lihat migration: onDelete('set null'))
        $name = $folder->name;
        $folder->delete();

        ActivityLog::log('folder', $request->user()->name . ' menghapus folder "' . $name . '"');

        return redirect()->route('folders.index')->with('success', 'Folder dihapus. Item di dalamnya dipindah ke "Tanpa Folder".');
    }
}
