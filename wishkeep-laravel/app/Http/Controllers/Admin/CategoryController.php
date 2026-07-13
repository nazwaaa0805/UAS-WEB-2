<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('wishlistItems as total_items')
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        $pendingCategories = Category::with('requestedBy')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.categories', [
            'categories' => $categories,
            'pendingCategories' => $pendingCategories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        try {
            Category::create([
                'name' => trim($validated['name']),
                'status' => 'approved',
            ]);
            return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan.');
        } catch (QueryException $e) {
            return redirect()->route('admin.categories')->with('error', 'Kategori dengan nama tersebut sudah ada.');
        }
    }

    public function approve(Request $request, Category $category)
    {
        $category->update(['status' => 'approved']);

        ActivityLog::log('category', $request->user()->name . ' menyetujui kategori "' . $category->name . '"');

        return redirect()->route('admin.categories')->with('success', 'Kategori "' . $category->name . '" disetujui dan siap dipakai.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dihapus.');
    }
}

