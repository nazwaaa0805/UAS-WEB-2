<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // photo_path sekarang menyimpan URL gambar hasil ambil otomatis dari
        // link produk (bukan lagi path file upload lokal), jadi perlu lebih panjang.
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->text('photo_path')->nullable()->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('link');
        });
    }
};
