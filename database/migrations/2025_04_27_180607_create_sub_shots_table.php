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
        Schema::create('sub_shots', function (Blueprint $table) {
            $table->id();
            $table->text('shot_id')->default('0');
            $table->text('user_id')->default('0');
            $table->text('sub_shot_number')->default('-');
            $table->text('sub_shot_description')->default('-');
            $table->text('video_file_path')->default('-');
            $table->text('duration')->default('-');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_shots');
    }
};
