<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_settings', function (Blueprint $table): void {
            $table->boolean('reminder_enabled')->default(false)->after('manual_confirmation_required');
            $table->unsignedSmallInteger('reminder_hours_before')->default(24)->after('reminder_enabled');
        });

        Schema::table('reservations', function (Blueprint $table): void {
            $table->timestamp('reminder_sent_at')->nullable()->after('cancel_token');
        });

        Schema::create('site_page_views', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('path', 512);
            $table->string('route_name', 128)->nullable();
            $table->timestamp('viewed_at');

            $table->index(['restaurant_id', 'viewed_at']);
            $table->index(['restaurant_id', 'path', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_page_views');

        Schema::table('reservations', function (Blueprint $table): void {
            $table->dropColumn('reminder_sent_at');
        });

        Schema::table('booking_settings', function (Blueprint $table): void {
            $table->dropColumn(['reminder_enabled', 'reminder_hours_before']);
        });
    }
};
