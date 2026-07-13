<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function wishlistFolders()
    {
        return $this->hasMany(WishlistFolder::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
