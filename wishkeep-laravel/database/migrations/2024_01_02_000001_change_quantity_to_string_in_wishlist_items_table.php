<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ganti kolom quantity dari angka (unsignedInteger) jadi teks (string),
     * supaya bisa diisi kalimat seperti "2 lusin" atau "12 buah", bukan cuma angka.
     *
     * Tidak pakai Schema::change() / renameColumn() supaya tidak butuh
     * paket doctrine/dbal (tidak terpasang di project ini).
     */
    public function up(): void
    {
        // 1) Simpan dulu nilai quantity lama sebelum kolomnya dihapus.
        $oldValues = DB::table('wishlist_items')->pluck('quantity', 'id');

        // 2) Hapus kolom lama (integer), lalu buat ulang sebagai string.
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->string('quantity', 50)->default('1')->after('price');
        });

        // 3) Isi kembali nilai lamanya (angka lama jadi teks, mis. 1 -> "1").
        foreach ($oldValues as $id => $value) {
            DB::table('wishlist_items')->where('id', $id)->update([
                'quantity' => (string) ($value ?? 1),
            ]);
        }
    }

    public function down(): void
    {
        $oldValues = DB::table('wishlist_items')->pluck('quantity', 'id');

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('price');
        });

        // Best-effort: ambil angka di depan teksnya (mis. "2 lusin" -> 2). Kalau
        // tidak ada angka di depan, fallback ke 1.
        foreach ($oldValues as $id => $value) {
            $number = 1;
            if (preg_match('/^\s*(\d+)/', (string) $value, $m)) {
                $number = max(1, (int) $m[1]);
            }
            DB::table('wishlist_items')->where('id', $id)->update([
                'quantity' => $number,
            ]);
        }
    }
};
