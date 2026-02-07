<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steering_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // 'claude', 'cursor', 'kiro', 'ai'
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        Schema::create('steering_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('steering_collection_id')->constrained()->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->text('content');
            $table->boolean('is_edited')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steering_docs');
        Schema::dropIfExists('steering_collections');
    }
};
