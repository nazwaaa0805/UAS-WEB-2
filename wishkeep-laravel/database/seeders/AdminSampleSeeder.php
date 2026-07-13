<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder contoh data untuk sisi ADMIN:
 *   - beberapa user tambahan (biar halaman /admin/users tidak cuma 1-2 baris)
 *   - kategori tambahan (langsung approved — di aplikasi ini kategori baru
 *     TIDAK butuh persetujuan admin, jadi tidak ada status "pending" di sini)
 *   - riwayat aktivitas dengan waktu bervariasi (biar /admin/activity-log tidak kosong)
 *
 * Aman dijalankan berkali-kali (idempotent).
 *
 * Cara pakai:
 *   php artisan db:seed --class=AdminSampleSeeder
 *
 * (Jalankan setelah SampleWishlistSeeder / DatabaseSeeder utama supaya user
 * "Budi Santoso" sudah ada. Kalau belum ada, seeder ini akan bikinkan sendiri.)
 */
class AdminSampleSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@wishlist.test'],
            ['name' => 'Admin Utama', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        $budi = User::firstOrCreate(
            ['email' => 'user@wishlist.test'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('user123'), 'role' => 'user']
        );

        // ===== USER TAMBAHAN =====
        $siti = User::firstOrCreate(
            ['email' => 'siti.rahma@wishlist.test'],
            ['name' => 'Siti Rahma', 'password' => Hash::make('user123'), 'role' => 'user']
        );

        $dedi = User::firstOrCreate(
            ['email' => 'dedi.kurniawan@wishlist.test'],
            ['name' => 'Dedi Kurniawan', 'password' => Hash::make('user123'), 'role' => 'user']
        );

        $rizka = User::firstOrCreate(
            ['email' => 'rizka.amelia@wishlist.test'],
            ['name' => 'Rizka Amelia', 'password' => Hash::make('user123'), 'role' => 'user']
        );

        // Kasih masing-masing 1 item wishlist sederhana biar kolom "Total Item"
        // di /admin/users tidak nol semua.
        $this->giveStarterItem($siti, 'Sepeda Lipat', 2200000, 'medium');
        $this->giveStarterItem($dedi, 'Kamera Mirrorless', 6500000, 'high');
        $this->giveStarterItem($rizka, 'Sepatu Sneakers', 650000, 'low');

        // ===== KATEGORI TAMBAHAN =====
        // Langsung 'approved' — user bisa bikin kategori baru sendiri tanpa
        // perlu persetujuan admin, jadi tidak ada data berstatus "pending" di sini.
        Category::firstOrCreate(['name' => 'Perlengkapan Camping'], ['status' => 'approved']);
        Category::firstOrCreate(['name' => 'Alat Musik'], ['status' => 'approved']);
        Category::firstOrCreate(['name' => 'Skincare & Kecantikan'], ['status' => 'approved']);
        Category::firstOrCreate(['name' => 'Perlengkapan Bayi'], ['status' => 'approved']);

        // ===== RIWAYAT AKTIVITAS =====
        // Kalau sudah pernah di-seed sebelumnya (ditandai salah satu deskripsi khas
        // di bawah sudah ada), jangan diulang supaya tidak dobel.
        $marker = 'Siti Rahma membuat folder "Persiapan Camping Akhir Tahun"';
        if (ActivityLog::where('description', $marker)->exists()) {
            return;
        }

        // [user, action, deskripsi, berapa hari yang lalu]
        $logs = [
            [$budi, 'create', 'Budi Santoso menambahkan item "Mesin Espresso Semi Otomatis"', 9],
            [$budi, 'folder', 'Budi Santoso membuat folder "Perlengkapan Dapur Cafe"', 9],
            [$siti, 'folder', 'Siti Rahma membuat folder "Persiapan Camping Akhir Tahun"', 8],
            [$siti, 'create', 'Siti Rahma menambahkan item "Tenda Dome 4 Orang"', 8],
            [$dedi, 'create', 'Dedi Kurniawan menambahkan item "Kamera Mirrorless"', 7],
            [$rizka, 'category', 'Rizka Amelia menambahkan kategori baru "Skincare & Kecantikan"', 6],
            [$budi, 'update', 'Budi Santoso mengubah item "Grinder Kopi Manual"', 6],
            [$dedi, 'category', 'Dedi Kurniawan menambahkan kategori baru "Alat Musik"', 5],
            [$siti, 'toggle', 'Siti Rahma menandai "Sepeda Lipat" sebagai Sudah Dibeli', 5],
            [$budi, 'saving', 'Budi Santoso menabung Rp100.000 untuk "Gamis Syar\'i"', 4],
            [$siti, 'category', 'Siti Rahma menambahkan kategori baru "Perlengkapan Camping"', 4],
            [$rizka, 'create', 'Rizka Amelia menambahkan item "Sepatu Sneakers"', 3],
            [$dedi, 'toggle', 'Dedi Kurniawan menandai "Kamera Mirrorless" sebagai Belum Dibeli', 3],
            [$budi, 'move', 'Budi Santoso memindahkan "Buku Novel Best Seller" ke folder "List Kado Ulang Tahun Rizka ke-21"', 2],
            [$siti, 'update', 'Siti Rahma mengubah item "Tenda Dome 4 Orang"', 2],
            [$budi, 'create', 'Budi Santoso menambahkan 6 barang lewat Catatan Belanja', 1],
            [$budi, 'category', 'Budi Santoso menambahkan kategori baru "Perlengkapan Bayi"', 1],
            [$dedi, 'delete', 'Dedi Kurniawan menghapus item "Tripod Kamera"', 1],
            [$rizka, 'saving', 'Rizka Amelia menabung Rp50.000 untuk "Sepatu Sneakers"', 0],
            [$budi, 'toggle', 'Budi Santoso menandai "Sarung Tenun" sebagai Sudah Dibeli', 0],
        ];

        foreach ($logs as [$user, $action, $description, $daysAgo]) {
            $log = new ActivityLog([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
            ]);
            $log->created_at = now()->subDays($daysAgo)->subMinutes(random_int(0, 600));
            $log->save();
        }
    }

    private function giveStarterItem(User $user, string $title, float $price, string $priority): void
    {
        if (WishlistItem::where('user_id', $user->id)->exists()) {
            return;
        }

        WishlistItem::create([
            'user_id' => $user->id,
            'category_id' => null,
            'folder_id' => null,
            'title' => $title,
            'description' => '',
            'price' => $price,
            'quantity' => '1',
            'saved_amount' => 0,
            'priority' => $priority,
            'status' => 'belum',
        ]);
    }
}
