<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\WishlistFolder;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // ===== DASHBOARD / LIST (dengan filter & search) =====
    public function dashboard(Request $request)
    {
        $userId = $request->user()->id;

        $query = WishlistItem::with(['category', 'folder'])->where('user_id', $userId);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->query('q') . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }
        if ($request->filled('folder_id')) {
            if ($request->query('folder_id') === 'none') {
                $query->whereNull('folder_id');
            } else {
                $query->where('folder_id', $request->query('folder_id'));
            }
        }

        $query->orderByDesc('created_at');

        $items = $query->get();

        $stats = [
            'totalItems' => $items->count(),
            'totalBelum' => $items->where('status', 'belum')->count(),
            'totalDibeli' => $items->where('status', 'dibeli')->count(),
            'totalEstimasi' => $items->where('status', 'belum')->sum('price'),
        ];

        // ===== Reminder H-7: folder dengan tanggal acara dalam 7 hari ke depan =====
        $upcomingReminders = $request->user()->wishlistFolders()
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('event_date')
            ->get();

        // ===== Reminder H-7: item wishlist dengan target_date dalam 7 hari ke depan =====
        $upcomingItemReminders = WishlistItem::where('user_id', $userId)
            ->where('status', 'belum')
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('target_date')
            ->get();

        return view('wishlist.dashboard', [
            'items' => $items,
            'categories' => Category::where('status', 'approved')->orderBy('name')->get(),
            'folders' => $request->user()->wishlistFolders()->orderBy('name')->get(),
            'filters' => $request->only(['q', 'status', 'priority', 'category_id', 'folder_id']),
            'stats' => $stats,
            'upcomingReminders' => $upcomingReminders,
            'upcomingItemReminders' => $upcomingItemReminders,
        ]);
    }

    // ===== FORM TAMBAH =====
    public function create(Request $request)
    {
        return view('wishlist.form', [
            'item' => null,
            'categories' => Category::where('status', 'approved')->orderBy('name')->get(),
            'folders' => $request->user()->wishlistFolders()->orderBy('name')->get(),
            'selectedFolderId' => $request->query('folder_id'),
        ]);
    }

    // ===== SIMPAN ITEM BARU =====
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'string', 'max:50'],
            'saved_amount' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'link' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'folder_id' => ['nullable', 'exists:wishlist_folders,id'],
            'status' => ['nullable', 'in:belum,dibeli'],
        ], [
            'title.required' => 'Judul item wajib diisi.',
        ]);

        $quantity = trim((string) ($validated['quantity'] ?? ''));

        $item = $request->user()->wishlistItems()->create([
            'category_id' => $validated['category_id'] ?? null,
            'folder_id' => $validated['folder_id'] ?? null,
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'] ?? 0,
            'quantity' => $quantity !== '' ? $quantity : '1',
            'saved_amount' => $validated['saved_amount'] ?? 0,
            'target_date' => $validated['target_date'] ?? null,
            'link' => $validated['link'] ?? '',
            'priority' => $validated['priority'] ?? 'medium',
            'status' => $validated['status'] ?? 'belum',
        ]);

        ActivityLog::log('create', $request->user()->name . ' menambahkan item "' . $item->title . '"');

        return redirect()->route('dashboard')->with('success', 'Item wishlist berhasil ditambahkan.');
    }

    // ===== FORM EDIT =====
    public function edit(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Item tidak ditemukan.');
        }

        return view('wishlist.form', [
            'item' => $wishlist,
            'categories' => Category::where('status', 'approved')->orderBy('name')->get(),
            'folders' => $request->user()->wishlistFolders()->orderBy('name')->get(),
            'selectedFolderId' => $wishlist->folder_id,
        ]);
    }

    // ===== UPDATE =====
    public function update(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->route('dashboard')->with('error', 'Item tidak ditemukan.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'string', 'max:50'],
            'saved_amount' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'link' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'folder_id' => ['nullable', 'exists:wishlist_folders,id'],
            'status' => ['nullable', 'in:belum,dibeli'],
        ], [
            'title.required' => 'Judul item wajib diisi.',
        ]);

        $quantity = trim((string) ($validated['quantity'] ?? ''));

        $wishlist->update([
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'] ?? 0,
            'quantity' => $quantity !== '' ? $quantity : '1',
            'saved_amount' => $validated['saved_amount'] ?? 0,
            'target_date' => $validated['target_date'] ?? null,
            'link' => $validated['link'] ?? '',
            'priority' => $validated['priority'] ?? 'medium',
            'category_id' => $validated['category_id'] ?? null,
            'folder_id' => $validated['folder_id'] ?? null,
            'status' => $validated['status'] ?? 'belum',
        ]);

        ActivityLog::log('update', $request->user()->name . ' mengubah item "' . $wishlist->title . '"');

        return redirect()->route('dashboard')->with('success', 'Item wishlist berhasil diperbarui.');
    }

    // ===== DELETE =====
    public function destroy(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        $title = $wishlist->title;
        $wishlist->delete();

        ActivityLog::log('delete', $request->user()->name . ' menghapus item "' . $title . '"');

        return redirect()->back()->with('success', 'Item wishlist berhasil dihapus.');
    }

    // ===== TOGGLE STATUS CEPAT (belum <-> dibeli) =====
    public function toggleStatus(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        $wishlist->update([
            'status' => $wishlist->status === 'belum' ? 'dibeli' : 'belum',
        ]);

        $statusText = $wishlist->status === 'dibeli' ? 'sudah dibeli' : 'belum dibeli';
        ActivityLog::log('toggle', $request->user()->name . ' menandai "' . $wishlist->title . '" sebagai ' . $statusText);

        return redirect()->back();
    }

    // ===== PINDAH FOLDER CEPAT (dari dashboard, tanpa buka form edit penuh) =====
    public function moveFolder(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        $validated = $request->validate([
            'folder_id' => ['nullable', 'exists:wishlist_folders,id'],
        ]);

        $newFolderId = $validated['folder_id'] ?? null;
        $folderName = 'Tanpa Folder';

        if ($newFolderId) {
            $folder = WishlistFolder::where('id', $newFolderId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (! $folder) {
                return redirect()->back()->with('error', 'Folder tidak ditemukan.');
            }

            $folderName = $folder->icon ? $folder->icon . ' ' . $folder->name : $folder->name;
        }

        $wishlist->update(['folder_id' => $newFolderId]);

        ActivityLog::log('move', $request->user()->name . ' memindahkan "' . $wishlist->title . '" ke folder "' . $folderName . '"');

        return redirect()->back()->with('success', 'Item dipindahkan ke "' . $folderName . '".');
    }

    // ===== CATATAN BELANJA (form tambah beberapa barang kecil sekaligus) =====
    public function quickAddForm(Request $request)
    {
        return view('wishlist.quick-add', [
            'folders' => $request->user()->wishlistFolders()->orderBy('name')->get(),
            'categories' => Category::where('status', 'approved')->orderBy('name')->get(),
            'selectedFolderId' => $request->query('folder_id'),
        ]);
    }

    public function quickAddStore(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => ['nullable', 'exists:wishlist_folders,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'string', 'max:50'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $folderId = $validated['folder_id'] ?? null;
        $categoryId = $validated['category_id'] ?? null;
        $created = 0;

        foreach ($validated['items'] as $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                continue; // lewati baris kosong
            }

            $quantity = trim((string) ($row['quantity'] ?? ''));

            $request->user()->wishlistItems()->create([
                'category_id' => $categoryId,
                'folder_id' => $folderId,
                'title' => $title,
                'description' => '',
                'price' => $row['price'] ?? 0,
                'quantity' => $quantity !== '' ? $quantity : '1',
                'saved_amount' => 0,
                'priority' => 'low',
                'status' => 'belum',
            ]);
            $created++;
        }

        if ($created === 0) {
            return redirect()->route('wishlist.quick-add')->with('error', 'Belum ada barang yang diisi. Isi minimal nama barang di satu baris.');
        }

        ActivityLog::log('create', $request->user()->name . ' menambahkan ' . $created . ' barang lewat Catatan Belanja');

        return redirect()->route('dashboard')->with('success', $created . ' barang berhasil ditambahkan ke wishlist.');
    }

    // ===== TAMBAH TABUNGAN CEPAT =====
    public function addSaving(Request $request, WishlistItem $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Item tidak ditemukan.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ], [
            'amount.required' => 'Jumlah nabung wajib diisi.',
            'amount.min' => 'Jumlah nabung minimal Rp1.',
        ]);

        $newSaved = min(
            (float) $wishlist->price,
            (float) $wishlist->saved_amount + (float) $validated['amount']
        );

        $wishlist->update(['saved_amount' => $newSaved]);

        ActivityLog::log('saving', $request->user()->name . ' menabung Rp' . number_format($validated['amount'], 0, ',', '.') . ' untuk "' . $wishlist->title . '"');

        $message = $newSaved >= (float) $wishlist->price
            ? 'Yeay, tabungan untuk "' . $wishlist->title . '" sudah mencapai target! 🎉'
            : 'Tabungan berhasil ditambahkan.';

        return redirect()->back()->with('success', $message);
    }

    // ===== FITUR CETAK (PRINT) =====
    public function print(Request $request)
    {
        $userId = $request->user()->id;
        $status = $request->query('status');

        $query = WishlistItem::with('category')->where('user_id', $userId);
        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('priority')->orderByDesc('created_at')->get();
        $totalEstimasi = $items->where('status', 'belum')->sum('price');

        return view('wishlist.print', [
            'items' => $items,
            'totalEstimasi' => $totalEstimasi,
            'printedAt' => now(),
            'user' => $request->user(),
            'statusFilter' => $status ?: 'semua',
        ]);
    }

    // ===== EXPORT CSV (bisa dibuka langsung di Excel) =====
    public function export(Request $request)
    {
        $items = WishlistItem::with(['category', 'folder'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        $filename = 'wishlist_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 supaya karakter (misal Rp, é, dll) tampil benar di Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Judul', 'Deskripsi', 'Kategori', 'Folder', 'Jumlah', 'Prioritas',
                'Status', 'Harga (Rp)', 'Link', 'Tanggal Ditambah',
            ]);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->title,
                    $item->description,
                    $item->category->name ?? '-',
                    $item->folder->name ?? '-',
                    $item->quantity ?? 1,
                    match ($item->priority) {
                        'high' => 'Tinggi',
                        'medium' => 'Sedang',
                        default => 'Rendah',
                    },
                    $item->status === 'dibeli' ? 'Sudah Dibeli' : 'Belum Dibeli',
                    number_format((float) $item->price, 0, ',', '.'),
                    $item->link,
                    $item->created_at->format('d-m-Y'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    // ===== SEARCH AUTOCOMPLETE (AJAX, balikin JSON) =====
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '' || strlen($q) < 2) {
            return response()->json([]);
        }

        $suggestions = WishlistItem::where('user_id', $request->user()->id)
            ->where('title', 'like', '%' . $q . '%')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'title', 'price', 'status'])
            ->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'price' => 'Rp' . number_format((float) $item->price, 0, ',', '.'),
                'status' => $item->status,
            ]);

        return response()->json($suggestions);
    }
}
