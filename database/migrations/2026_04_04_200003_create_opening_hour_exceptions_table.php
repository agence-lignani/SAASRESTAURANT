<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_hour_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->date('exception_date');
            $table->boolean('is_closed')->default(false);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['restaurant_id', 'exception_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_hour_exceptions');
    }
};
