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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('garage_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('alert_type', ['closure', 'construction', 'event', 'maintenance', 'full'])->default('event');
            $table->string('title');
            $table->text('details')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
