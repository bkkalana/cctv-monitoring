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
            $table->foreignId('camera_id')->constrained();
            $table->string('snapshot_path');
            $table->decimal('confidence', 5, 2)->nullable();
            $table->string('type')->default('unknown_face');
            $table->boolean('is_recognized')->default(false);
            $table->foreignId('face_id')->nullable()->constrained();
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
