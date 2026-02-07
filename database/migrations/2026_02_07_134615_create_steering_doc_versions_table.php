<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steering_doc_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('steering_doc_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->string('change_type')->default('updated'); // created, updated, crawled
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steering_doc_versions');
    }
};
