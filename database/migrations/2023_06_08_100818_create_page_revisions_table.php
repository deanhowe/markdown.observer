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

        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->text('markdown_content');
            $table->text('html_content');
            $table->json('tiptap_json')->nullable();
            $table->string('revision_type')->default('update'); // 'create', 'update', 'delete', 'conflict'
            $table->timestamps();

            // Index for faster lookups
            $table->index('filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_revisions');
    }
};
