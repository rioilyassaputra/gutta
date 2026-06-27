<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 50);
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->integer('province_id');
            $table->string('province_name', 100);
            $table->integer('city_id');
            $table->string('city_name', 100);
            $table->string('district', 100);
            $table->string('postal_code', 10);
            $table->text('full_address');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
