<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistFolder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'photo_path',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function isCatatanBelanja(): bool
    {
        return $this->type === 'catatan_belanja';
    }

    /**
     * Nama folder buat ditampilkan di dropdown/list, dikasih label "(C)"
     * kalau tipenya Catatan Belanja biar beda sama folder biasa.
     */
    public function displayName(): string
    {
        $name = ($this->icon ? $this->icon . ' ' : '') . $this->name;

        return $this->isCatatanBelanja() ? $name . ' (C)' : $name;
    }

    /**
     * URL publik foto tema folder, atau null kalau belum ada.
     */
    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

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
