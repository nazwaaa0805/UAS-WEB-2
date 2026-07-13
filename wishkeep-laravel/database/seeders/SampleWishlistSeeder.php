<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\WishlistFolder;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder contoh data: folder + item wishlist + "catatan belanja".
 *
 * Aman dijalankan berkali-kali (idempotent) — folder yang namanya sudah ada
 * tidak akan dibuat ulang / tidak akan menggandakan item.
 *
 * Cara pakai:
 *   php artisan db:seed --class=SampleWishlistSeeder
 */
class SampleWishlistSeeder extends Seeder
{
    public function run(): void
    {
        // Pakai user demo yang sama dengan DatabaseSeeder utama (Budi Santoso).
        // Kalau belum ada (mis. baru migrate:fresh tanpa seed utama), dibuatkan dulu.
        $user = User::firstOrCreate(
            ['email' => 'user@wishlist.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        $categories = collect([
            'Dapur & Cafe',
            'Alat Tulis',
            'Fashion',
            'Kado & Hadiah',
            'Rumah Tangga',
        ])->mapWithKeys(function ($name) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                ['status' => 'approved']
            );

            return [$name => $category->id];
        });

        $this->seedFolder(
            user: $user,
            folderName: 'Perlengkapan Dapur Cafe',
            icon: '☕',
            eventDate: null,
            categoryId: $categories['Dapur & Cafe'],
            items: [
                ['Mesin Espresso Semi Otomatis', 'Buat bikin espresso & steam susu sendiri', 4500000, 0, '1', 'high', 'belum'],
                ['Grinder Kopi Manual', 'Gilingan biji kopi harian', 350000, 350000, '1', 'medium', 'dibeli'],
                ['Milk Frother Elektrik', 'Untuk foam susu cappuccino/latte', 180000, 0, '2 buah', 'low', 'belum'],
                ['French Press 600ml', 'Cadangan buat meja seduh manual', 150000, 150000, '3 buah', 'medium', 'dibeli'],
                ['Cangkir Cappuccino Set', 'Set cangkir + saucer buat dine-in', 480000, 0, '12 buah', 'low', 'belum'],
                ['Timbangan Digital Kopi', 'Takaran presisi biji & air', 220000, 0, '1', 'medium', 'belum'],
                ['Termometer Susu', 'Cek suhu steam susu', 65000, 65000, '2 buah', 'low', 'dibeli'],
            ],
        );

        $this->seedFolder(
            user: $user,
            folderName: 'Peralatan Tulis Anak SD',
            icon: '✏️',
            eventDate: now()->addWeeks(3),
            categoryId: $categories['Alat Tulis'],
            items: [
                ['Buku Tulis 38 Lembar', 'Buat semester baru', 45000, 0, '2 lusin', 'high', 'belum'],
                ['Pensil 2B', 'Wajib buat ulangan & LJK', 24000, 0, '1 lusin', 'high', 'belum'],
                ['Penghapus Karet', '', 15000, 0, '6 buah', 'medium', 'belum'],
                ['Penggaris 30cm', '', 8000, 8000, '3 buah', 'low', 'dibeli'],
                ['Tas Sekolah Anak', 'Model ransel, ada roda', 175000, 0, '1', 'high', 'belum'],
                ['Kotak Pensil', '', 35000, 35000, '1', 'medium', 'dibeli'],
                ['Krayon 24 Warna', 'Buat pelajaran SBK', 42000, 0, '2 buah', 'medium', 'belum'],
                ['Buku Gambar A4', '', 18000, 0, '1 pack', 'low', 'belum'],
            ],
        );

        $this->seedFolder(
            user: $user,
            folderName: 'Outfit Lebaran',
            icon: '🎉',
            eventDate: now()->addMonths(1),
            categoryId: $categories['Fashion'],
            items: [
                ['Baju Koko Lengan Panjang', 'Buat sholat Ied', 275000, 0, '1', 'high', 'belum'],
                ['Gamis Syar\'i', 'Warna pastel', 320000, 100000, '1', 'high', 'belum'],
                ['Mukena Bordir', '', 250000, 0, '1', 'medium', 'belum'],
                ['Sarung Tenun', '', 180000, 180000, '2 buah', 'medium', 'dibeli'],
                ['Sepatu Pantofel', '', 350000, 0, '1', 'medium', 'belum'],
                ['Peci Rajut', '', 65000, 65000, '1', 'low', 'dibeli'],
                ['Jilbab Segi Empat', 'Buat 3 hari raya berturut-turut', 45000, 0, '3 buah', 'low', 'belum'],
            ],
        );

        $this->seedFolder(
            user: $user,
            folderName: 'List Kado Ulang Tahun Rizka ke-21',
            icon: '🎂',
            eventDate: now()->addDays(4), // sengaja < 7 hari biar muncul di reminder dashboard
            categoryId: $categories['Kado & Hadiah'],
            items: [
                ['Skincare Set Glow', 'Rizka lagi hobi skincare-an', 350000, 100000, '1 set', 'high', 'belum'],
                ['Buku Novel Best Seller', 'Genre romance/fiksi favoritnya', 95000, 0, '1', 'medium', 'belum'],
                ['Buket Bunga Mawar', 'Ambil H-1 biar masih segar', 150000, 0, '1', 'high', 'belum'],
                ['Kue Ulang Tahun Custom', 'Custom nama & tulisan "Happy 21st"', 275000, 0, '1', 'high', 'belum'],
                ['Dompet Kulit Wanita', '', 220000, 220000, '1', 'medium', 'dibeli'],
                ['Parfum Mini Set', 'Travel size, isi 3 varian', 180000, 0, '1 set', 'low', 'belum'],
                ['Kartu Ucapan Custom', '', 25000, 25000, '2 buah', 'low', 'dibeli'],
            ],
        );

        // ===== CATATAN BELANJA =====
        // Contoh hasil dari fitur "Catatan Belanja" (quick-add): barang-barang kecil,
        // tanpa folder, jumlahnya boleh berupa kalimat (bukan cuma angka).
        $this->seedFolder(
            user: $user,
            folderName: null, // tanpa folder, persis seperti hasil Catatan Belanja
            icon: null,
            eventDate: null,
            categoryId: $categories['Rumah Tangga'],
            items: [
                ['Beras 5kg', '', 68000, 0, '1 karung', 'low', 'belum'],
                ['Telur Ayam', '', 30000, 0, '1 kg', 'low', 'belum'],
                ['Minyak Goreng', '', 32000, 0, '2 liter', 'low', 'belum'],
                ['Gula Pasir', '', 15000, 15000, '1 kg', 'low', 'dibeli'],
                ['Sabun Cuci Piring', '', 12000, 0, '3 buah', 'low', 'belum'],
                ['Tisu Wajah', '', 9000, 0, '4 pack', 'low', 'belum'],
            ],
        );
    }

    /**
     * Buat 1 folder (atau tanpa folder kalau $folderName null) beserta item-itemnya.
     * Item format tiap baris: [judul, deskripsi, harga, sudah_menabung, jumlah, prioritas, status]
     */
    private function seedFolder(
        User $user,
        ?string $folderName,
        ?string $icon,
        $eventDate,
        int $categoryId,
        array $items,
    ): void {
        $folderId = null;

        if ($folderName !== null) {
            $folder = WishlistFolder::firstOrCreate(
                ['user_id' => $user->id, 'name' => $folderName],
                ['icon' => $icon, 'event_date' => $eventDate]
            );
            $folderId = $folder->id;

            // Kalau folder-nya baru saja ada (bukan baru dibuat) tapi sudah punya
            // item, anggap sudah pernah di-seed sebelumnya -> jangan dobel.
            if (WishlistItem::where('user_id', $user->id)->where('folder_id', $folderId)->exists()) {
                return;
            }
        } else {
            // Folder null (Catatan Belanja): cek pakai judul item pertama supaya
            // tidak nge-seed ulang tiap kali dijalankan.
            $firstTitle = $items[0][0] ?? null;
            if ($firstTitle && WishlistItem::where('user_id', $user->id)
                ->whereNull('folder_id')
                ->where('title', $firstTitle)
                ->exists()) {
                return;
            }
        }

        foreach ($items as [$title, $description, $price, $savedAmount, $quantity, $priority, $status]) {
            WishlistItem::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'folder_id' => $folderId,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'saved_amount' => $savedAmount,
                'quantity' => $quantity,
                'priority' => $priority,
                'status' => $status,
            ]);
        }
    }
}
