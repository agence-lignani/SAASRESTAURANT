<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->string('source', 20)->nullable()->after('status');
            $table->string('external_id')->nullable()->after('source');
            $table->json('external_payload')->nullable()->after('external_id');
            $table->timestamp('synced_at')->nullable()->after('external_payload');

            $table->index(['restaurant_id', 'source']);
            $table->unique(['restaurant_id', 'source', 'external_id'], 'reservations_rest_source_external_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->dropUnique('reservations_rest_source_external_unique');
            $table->dropIndex(['restaurant_id', 'source']);
            $table->dropColumn(['source', 'external_id', 'external_payload', 'synced_at']);
        });
    }
};
