<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_user', function (Blueprint $table): void {
            $table->index(['user_id', 'role', 'restaurant_id'], 'restaurant_user_user_role_restaurant_index');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_user', function (Blueprint $table): void {
            $table->dropIndex('restaurant_user_user_role_restaurant_index');
        });
    }
};
