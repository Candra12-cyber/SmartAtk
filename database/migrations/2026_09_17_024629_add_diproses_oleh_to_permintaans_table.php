<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaans', function (Blueprint $table) {
            $table->foreignId('diproses_oleh')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permintaans', function (Blueprint $table) {
            $table->dropForeign(['diproses_oleh']);
            $table->dropColumn('diproses_oleh');
        });
    }
};