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
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['usb', 'ip']);
            $table->string('device_id')->nullable(); // For USB cameras
            $table->string('rtsp_url')->nullable(); // For IP cameras
            $table->string('stream_url')->nullable(); // Generated stream URL from Python service
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
