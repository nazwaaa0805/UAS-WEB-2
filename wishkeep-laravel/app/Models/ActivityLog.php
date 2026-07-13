<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // cuma pakai created_at, di-set manual/default DB

    // Karena $timestamps di-nonaktifkan, Eloquent TIDAK otomatis meng-cast
    // created_at jadi objek Carbon (itu sebabnya ->locale() di view sempat error
    // "Call to a member function locale() on string"). Cast manual di sini.
    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper cepat buat nyatet aktivitas dari controller mana pun.
     * Contoh: ActivityLog::log('create', 'Menambahkan item "Sepatu Lari"');
     */
    public static function log(string $action, string $description): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }
}
