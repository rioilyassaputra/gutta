<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additional performance indexes — run after all tables are created.
     */
    public function up(): void
    {
        // users
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('role');
        });

        // orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
        });

        // product_variants — product_id index already added in create migration
        // orders.tracking_number — index already added in create migration

        // cart_items — user_id & session_id indexes already added in create migration
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
