<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('status')->default('pending'); // pending, processed, done
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
