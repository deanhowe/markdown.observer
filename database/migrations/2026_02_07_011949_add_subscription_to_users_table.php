<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_tier')->default('free'); // free, pro, lifetime
            $table->integer('upload_limit')->default(2); // 2 for free
            $table->integer('doc_limit')->default(10); // 10 for free
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_tier',
                'upload_limit',
                'doc_limit',
                'stripe_customer_id',
                'stripe_subscription_id',
                'subscription_ends_at',
            ]);
        });
    }
};
