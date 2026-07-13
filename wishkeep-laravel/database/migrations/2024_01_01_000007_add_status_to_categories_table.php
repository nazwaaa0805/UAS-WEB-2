<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('status', ['approved', 'pending'])->default('approved')->after('name');
            $table->foreignId('requested_by')->nullable()->after('status')
                ->constrained('users')->onDelete('set null');
        });

        // Kategori yang sudah ada sebelumnya (dibuat admin lewat seeder) otomatis approved
        \DB::table('categories')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('requested_by');
            $table->dropColumn('status');
        });
    }
};
