<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_addons', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('appointment_id')->constrained('services')->onDelete('set null');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_addons', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
