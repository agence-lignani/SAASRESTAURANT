<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('color_primary', 32)->nullable();
            $table->string('color_secondary', 32)->nullable();
            $table->string('color_accent', 32)->nullable();
            $table->string('color_background', 32)->nullable();
            $table->string('color_text', 32)->nullable();
            $table->string('radius_sm', 16)->nullable();
            $table->string('radius_md', 16)->nullable();
            $table->string('radius_lg', 16)->nullable();
            $table->string('font_heading_key', 64)->nullable();
            $table->string('font_body_key', 64)->nullable();
            $table->timestamps();

            $table->unique('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_theme_settings');
    }
};
