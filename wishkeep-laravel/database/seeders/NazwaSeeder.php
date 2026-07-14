<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WishlistFolder;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NazwaSeeder extends Seeder
{
    public function run(): void
    {
        // Buat / pastikan user Nazwa ada
        $user = User::updateOrCreate(
            ['email' => 'nazwa@gmail.com'],
            [
                'name' => 'Nazwa',
                'password' => Hash::make('nazwa12345'),
                'role' => 'user',
            ]
        );

        // Kosongkan dulu folder & item lama milik Nazwa (biar seeder ini aman dijalankan berkali-kali)
        WishlistItem::where('user_id', $user->id)->delete();
        WishlistFolder::where('user_id', $user->id)->delete();

        // ===== 1. FOLDER =====

        $folderPantai = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Outfit Pantai',
            'icon' => '🏖️',
        ]);

        $folderLebaran = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Baju Lebaran',
            'icon' => '🌙',
            'event_date' => now()->addMonths(2),
        ]);

        $folderBunga = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'List Bunga untuk di Toko',
            'icon' => '💐',
        ]);

        $folderAlatTulis = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Perlengkapan Alat Tulis',
            'icon' => '✏️',
        ]);

        $folderTitipanMamah = WishlistFolder::create([
            'user_id' => $user->id,
            'name' => 'Titipan Mamah',
            'icon' => '🛒',
        ]);

        // ===== Item folder: Outfit Pantai =====
        $this->items($user->id, $folderPantai->id, [
            ['Dress Pantai Motif Bunga', 150000, 'medium'],
            ['Topi Pantai Anyaman', 45000, 'low'],
            ['Kacamata Hitam', 85000, 'medium'],
            ['Sandal Jepit Pantai', 35000, 'low'],
        ]);

        // ===== Item folder: Baju Lebaran =====
        $this->items($user->id, $folderLebaran->id, [
            ['Baju Gamis Lebaran', 350000, 'high'],
            ['Mukena Baru', 250000, 'medium'],
            ['Sarung Motif Baru', 120000, 'low'],
            ['Hijab Pashmina', 60000, 'low'],
        ]);

        // ===== Item folder: List Bunga untuk di Toko (perintilan, pakai jumlah) =====
        $this->quickItems($user->id, $folderBunga->id, [
            ['Bunga Mawar Segar', '5 ikat', 150000],
            ['Bunga Melati', '2 kg', 80000],
            ['Pita Dekorasi', '10 gulung', 50000],
            ['Vas Bunga Kaca', '6 buah', 120000],
            ['Floral Foam', '1 dus', 60000],
        ]);

        // ===== 2. CATATAN BELANJA =====

        // Perlengkapan Alat Tulis
        $this->quickItems($user->id, $folderAlatTulis->id, [
            ['Pulpen Pilot', '1 lusin', 24000],
            ['Buku Tulis 38 Lembar', '12 buah', 36000],
            ['Penghapus', '5 buah', 0],
            ['Correction Tape', '3 buah', 15000],
            ['Stabilo', '1 set', 20000],
        ]);

        // Titipan Mamah
        $this->quickItems($user->id, $folderTitipanMamah->id, [
            ['Beras', '1 karung 5kg', 65000],
            ['Minyak Goreng', '2 liter', 32000],
            ['Gula Pasir', '2 kg', 28000],
            ['Bawang Merah', '1 kg', 0],
            ['Telur Ayam', '1 kg', 27000],
        ]);
    }

    /**
     * Buat beberapa item wishlist biasa (kayak dari form Tambah Item).
     * @param array<int, array{0:string,1:int,2:string}> $rows [judul, harga, prioritas]
     */
    private function items(int $userId, int $folderId, array $rows): void
    {
        foreach ($rows as [$title, $price, $priority]) {
            WishlistItem::create([
                'user_id' => $userId,
                'folder_id' => $folderId,
                'title' => $title,
                'description' => '',
                'price' => $price,
                'quantity' => '1',
                'saved_amount' => 0,
                'link' => '',
                'priority' => $priority,
                'status' => 'belum',
            ]);
        }
    }

    /**
     * Buat beberapa item ala "Catatan Belanja" (barang perintilan dengan jumlah bebas teks).
     * @param array<int, array{0:string,1:string,2:int}> $rows [judul, jumlah, harga (0 = opsional/kosong)]
     */
    private function quickItems(int $userId, int $folderId, array $rows): void
    {
        foreach ($rows as [$title, $quantity, $price]) {
            WishlistItem::create([
                'user_id' => $userId,
                'folder_id' => $folderId,
                'title' => $title,
                'description' => '',
                'price' => $price,
                'quantity' => $quantity,
                'saved_amount' => 0,
                'link' => '',
                'priority' => 'low',
                'status' => 'belum',
            ]);
        }
    }
}
