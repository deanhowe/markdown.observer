<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('package_name'); // e.g., 'laravel/framework'
            $table->string('version')->nullable(); // e.g., '^12.0'
            $table->string('type'); // 'composer' or 'npm'
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'package_name', 'type']);
        });

        Schema::create('package_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('package_name');
            $table->string('file_path'); // e.g., 'docs/installation.md'
            $table->text('content');
            $table->text('original_content')->nullable(); // For tracking edits
            $table->string('upstream_hash')->nullable(); // For sync detection
            $table->boolean('is_edited')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'package_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_docs');
        Schema::dropIfExists('user_packages');
    }
};
