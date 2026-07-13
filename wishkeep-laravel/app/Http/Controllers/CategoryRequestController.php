<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryRequestController extends Controller
{
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
                'requested_by' => $request->user()->id,
            ]);

            ActivityLog::log('category', $request->user()->name . ' menambahkan kategori baru "' . trim($validated['name']) . '"');

            return back()->with('success', 'Kategori "' . trim($validated['name']) . '" berhasil ditambahkan dan langsung bisa dipakai.');
        } catch (QueryException $e) {
            return back()->with('error', 'Kategori dengan nama tersebut sudah ada.');
        }
    }
}
