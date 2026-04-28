<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_theme_settings', function (Blueprint $table): void {
            $table->dropColumn(['color_accent', 'color_background']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_theme_settings', function (Blueprint $table): void {
            $table->string('color_accent', 32)->nullable()->after('color_secondary');
            $table->string('color_background', 32)->nullable()->after('color_accent');
        });
    }
};
