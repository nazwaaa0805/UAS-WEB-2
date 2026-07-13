<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistFolder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WishlistItem::class, 'folder_id');
    }

    /**
     * Hitung sisa hari menuju event_date. Null kalau tidak ada tanggal
     * atau event sudah lewat.
     */
    public function daysUntilEvent(): ?int
    {
        if (! $this->event_date) {
            return null;
        }

        $diff = now()->startOfDay()->diffInDays($this->event_date->startOfDay(), false);

        return $diff >= 0 ? $diff : null;
    }
}
