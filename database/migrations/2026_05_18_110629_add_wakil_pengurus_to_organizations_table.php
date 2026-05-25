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
        Schema::table('organizations', function (Blueprint $table) {
            // Wakil Ketua (5 slot)
            $table->foreignId('wakil_ketua_1_id')->nullable()->after('ketua_id')->references('id')->on('users');
            $table->foreignId('wakil_ketua_2_id')->nullable()->after('wakil_ketua_1_id')->references('id')->on('users');
            $table->foreignId('wakil_ketua_3_id')->nullable()->after('wakil_ketua_2_id')->references('id')->on('users');
            $table->foreignId('wakil_ketua_4_id')->nullable()->after('wakil_ketua_3_id')->references('id')->on('users');
            $table->foreignId('wakil_ketua_5_id')->nullable()->after('wakil_ketua_4_id')->references('id')->on('users');

            // Wakil Sekretaris (5 slot)
            $table->foreignId('wakil_sekretaris_1_id')->nullable()->after('sekretaris_id')->references('id')->on('users');
            $table->foreignId('wakil_sekretaris_2_id')->nullable()->after('wakil_sekretaris_1_id')->references('id')->on('users');
            $table->foreignId('wakil_sekretaris_3_id')->nullable()->after('wakil_sekretaris_2_id')->references('id')->on('users');
            $table->foreignId('wakil_sekretaris_4_id')->nullable()->after('wakil_sekretaris_3_id')->references('id')->on('users');
            $table->foreignId('wakil_sekretaris_5_id')->nullable()->after('wakil_sekretaris_4_id')->references('id')->on('users');

            // Wakil Bendahara (3 slot dulu)
            $table->foreignId('wakil_bendahara_1_id')->nullable()->after('bendahara_id')->references('id')->on('users');
            $table->foreignId('wakil_bendahara_2_id')->nullable()->after('wakil_bendahara_1_id')->references('id')->on('users');
            $table->foreignId('wakil_bendahara_3_id')->nullable()->after('wakil_bendahara_2_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Wakil Ketua
            $table->dropColumn([
                'wakil_ketua_1_id',
                'wakil_ketua_2_id',
                'wakil_ketua_3_id',
                'wakil_ketua_4_id',
                'wakil_ketua_5_id'
            ]);
            // Wakil Sekretaris
            $table->dropColumn([
                'wakil_sekretaris_1_id',
                'wakil_sekretaris_2_id',
                'wakil_sekretaris_3_id',
                'wakil_sekretaris_4_id',
                'wakil_sekretaris_5_id'
            ]);
            // Wakil Bendahara
            $table->dropColumn([
                'wakil_bendahara_1_id',
                'wakil_bendahara_2_id',
                'wakil_bendahara_3_id'
            ]);
        });
    }
};
