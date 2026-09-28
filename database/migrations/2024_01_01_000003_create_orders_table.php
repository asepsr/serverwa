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
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 50)->unique();
            $table->decimal('total_price', 10, 2);
            $table->string('status', 50)->default('pending');
            $table->string('payment_status', 20)->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_proof_url', 500)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->datetime('ordered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
