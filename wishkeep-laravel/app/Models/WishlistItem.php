<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'folder_id',
        'title',
        'description',
        'price',
        'quantity',
        'saved_amount',
        'target_date',
        'link',
        'priority',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'saved_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function folder()
    {
        return $this->belongsTo(WishlistFolder::class, 'folder_id');
    }

    /**
     * Persentase progress tabungan (0-100), dibulatkan.
     * Kalau harga 0, dianggap 0% (hindari pembagian dengan nol).
     */
    public function savingProgress(): int
    {
        if ((float) $this->price <= 0) {
            return 0;
        }

        $percent = ((float) $this->saved_amount / (float) $this->price) * 100;

        return (int) min(100, round($percent));
    }

    /**
     * Sisa hari menuju target_date. Null kalau tidak ada tanggal
     * atau tanggalnya sudah lewat.
     */
    public function daysUntilTarget(): ?int
    {
        if (! $this->target_date) {
            return null;
        }

        $diff = now()->startOfDay()->diffInDays($this->target_date->startOfDay(), false);

        return $diff >= 0 ? $diff : null;
    }
}
