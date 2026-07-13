<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // ===== Data grafik 1: total estimasi harga per kategori =====
        $perCategory = WishlistItem::with('category')
            ->where('user_id', $userId)
            ->get()
            ->groupBy(fn ($item) => $item->category->name ?? 'Tanpa Kategori')
            ->map(fn ($group) => (float) $group->sum('price'))
            ->sortDesc();

        // ===== Data grafik 2: perbandingan status belum vs sudah dibeli =====
        $items = WishlistItem::where('user_id', $userId)->get();
        $statusCount = [
            'belum' => $items->where('status', 'belum')->count(),
            'dibeli' => $items->where('status', 'dibeli')->count(),
        ];

        // ===== Data grafik 3: jumlah item per prioritas =====
        $priorityCount = [
            'high' => $items->where('priority', 'high')->count(),
            'medium' => $items->where('priority', 'medium')->count(),
            'low' => $items->where('priority', 'low')->count(),
        ];

        return view('stats.index', [
            'categoryLabels' => $perCategory->keys(),
            'categoryValues' => $perCategory->values(),
            'statusCount' => $statusCount,
            'priorityCount' => $priorityCount,
            'totalItems' => $items->count(),
        ]);
    }
}
