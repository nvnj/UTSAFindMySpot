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
        Schema::table('lots', function (Blueprint $table) {
            $table->string('space_type')->nullable()->after('type');
        });

        Schema::table('garages', function (Blueprint $table) {
            $table->string('space_type')->nullable()->after('levels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn('space_type');
        });

        Schema::table('garages', function (Blueprint $table) {
            $table->dropColumn('space_type');
        });
    }
};
