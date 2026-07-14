<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlist_folders', function (Blueprint $table) {
            $table->enum('type', ['biasa', 'catatan_belanja'])->default('biasa')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('wishlist_folders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
