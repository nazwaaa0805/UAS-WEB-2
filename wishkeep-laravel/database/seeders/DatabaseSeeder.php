<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\WishlistFolder;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) {
            return; // Sudah ada data, jangan seed ulang
        }

        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@wishlist.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'user@wishlist.test',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        $categoryNames = ['Elektronik', 'Buku', 'Fashion', 'Hobi', 'Rumah Tangga'];
        $categories = collect($categoryNames)->mapWithKeys(function ($name) {
            return [$name => Category::create(['name' => $name])->id];
        });

        $folderUlangTahun = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Ulang Tahun ke-23',
            'icon' => '🎂',
            'event_date' => now()->addDays(5),
        ]);

        $folderLebaran = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Lebaran Tahun Ini',
            'icon' => '🎉',
            'event_date' => now()->addMonths(2),
        ]);

        WishlistItem::create([
            'user_id' => $user->id,
            'category_id' => $categories['Elektronik'],
            'folder_id' => $folderUlangTahun->id,
            'title' => 'Headphone Noise Cancelling',
            'description' => 'Untuk kerja & belajar fokus',
            'price' => 1500000,
            'saved_amount' => 600000,
            'link' => 'https://example.com/headphone',
            'priority' => 'high',
            'status' => 'belum',
        ]);

        WishlistItem::create([
            'user_id' => $user->id,
            'category_id' => $categories['Buku'],
            'folder_id' => null,
            'title' => 'Buku Clean Code',
            'description' => 'Referensi belajar ngoding rapi',
            'price' => 220000,
            'link' => 'https://example.com/buku',
            'priority' => 'medium',
            'status' => 'belum',
        ]);

        WishlistItem::create([
            'user_id' => $user->id,
            'category_id' => $categories['Hobi'],
            'folder_id' => $folderLebaran->id,
            'title' => 'Sepatu Lari',
            'description' => 'Buat olahraga pagi',
            'price' => 450000,
            'link' => '',
            'priority' => 'low',
            'status' => 'dibeli',
        ]);
    }
}
