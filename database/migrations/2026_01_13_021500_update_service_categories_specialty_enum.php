<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Temporarily allow old and new values
        Schema::table('service_categories', function (Blueprint $table) {
            $table->enum('specialty', ['hair', 'nail', 'both', 'spa', 'all'])->default('both')->change();
        });

        // Normalize legacy "both" to "all" for full service (if any)
        DB::table('service_categories')
            ->where('name', 'Full Service')
            ->update(['specialty' => 'all']);

        // Update spa category if present
        DB::table('service_categories')
            ->where('name', 'Spa Services')
            ->update(['specialty' => 'spa']);

        // Lock enum to new set (without legacy "both")
        Schema::table('service_categories', function (Blueprint $table) {
            $table->enum('specialty', ['hair', 'nail', 'spa', 'all'])->default('all')->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->enum('specialty', ['hair', 'nail', 'both'])->default('both')->change();
        });
    }
};
