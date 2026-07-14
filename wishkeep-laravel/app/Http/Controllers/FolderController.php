<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\WishlistFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'type' => ['nullable', 'in:biasa,catatan_belanja'],
            'icon' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'event_date' => ['nullable', 'date'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
            'photo.image' => 'File foto harus berupa gambar (jpg, png, dll).',
            'photo.max' => 'Ukuran foto maksimal 4MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('folder-photos', 'public');
        }

        $folder = $request->user()->wishlistFolders()->create([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? 'biasa',
            'icon' => $validated['icon'] ?? null,
            'photo_path' => $photoPath,
            'event_date' => $validated['event_date'] ?? null,
        ]);

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
            'type' => ['nullable', 'in:biasa,catatan_belanja'],
            'icon' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
            'event_date' => ['nullable', 'date'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
            'photo.image' => 'File foto harus berupa gambar (jpg, png, dll).',
            'photo.max' => 'Ukuran foto maksimal 4MB.',
        ]);

        $photoPath = $folder->photo_path;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('folder-photos', 'public');
        } elseif ($request->boolean('remove_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = null;
        }

        $folder->update([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? 'biasa',
            'icon' => $validated['icon'] ?? null,
            'photo_path' => $photoPath,
            'event_date' => $validated['event_date'] ?? null,
        ]);

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

        if ($folder->photo_path) {
            Storage::disk('public')->delete($folder->photo_path);
        }

        $folder->delete();

        ActivityLog::log('folder', $request->user()->name . ' menghapus folder "' . $name . '"');

        return redirect()->route('folders.index')->with('success', 'Folder dihapus. Item di dalamnya dipindah ke "Tanpa Folder".');
    }
}
