<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->Integer('order_id'); 
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->Integer('product_id'); 
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
