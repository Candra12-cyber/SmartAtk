<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('nip');
            $table->string('jabatan')->nullable()->after('role');
            $table->string('unit_kerja')->nullable()->after('jabatan');
            $table->string('telepon')->nullable()->after('unit_kerja');
            $table->string('foto')->nullable()->after('telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'jabatan', 'unit_kerja', 'telepon', 'foto']);
        });
    }
};