<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: temporarily extend enum to include both old and new values
        Schema::table('staff', function (Blueprint $table) {
            $table->enum('specialty', [
                'hair',
                'nail',
                'both', // legacy
                'spa',
                'hair_nail',
                'hair_spa',
                'nail_spa',
                'all',
            ])->default('both')->change();
        });

        // Step 2: normalize legacy values
        DB::table('staff')
            ->where('specialty', 'both')
            ->update(['specialty' => 'all']);

        // Step 3: lock enum to the new set
        Schema::table('staff', function (Blueprint $table) {
            $table->enum('specialty', [
                'hair',
                'nail',
                'spa',
                'hair_nail',
                'hair_spa',
                'nail_spa',
                'all',
            ])->default('all')->change();
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->enum('specialty', ['hair', 'nail', 'both'])->default('both')->change();
        });
    }
};
