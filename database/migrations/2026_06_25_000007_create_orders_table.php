<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique()->index();
            $table->foreignId('user_id')->constrained();
            $table->json('address_snapshot');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('courier', 50);
            $table->string('courier_service', 50);
            $table->enum('status', [
                'pending_payment',
                'paid',
                'processing',
                'shipped',
                'completed',
                'cancelled',
            ])->default('pending_payment')->index();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_token', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('tracking_number', 50)->nullable()->index();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
