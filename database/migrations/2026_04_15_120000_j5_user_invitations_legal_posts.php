<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('role', 32);
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'email']);
        });

        Schema::create('legal_pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 64);
            $table->string('title');
            $table->longText('body');
            $table->timestamps();

            $table->unique(['restaurant_id', 'slug']);
        });

        Schema::create('site_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title', 180)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'slug']);
            $table->index(['restaurant_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_posts');
        Schema::dropIfExists('legal_pages');
        Schema::dropIfExists('user_invitations');
    }
};
