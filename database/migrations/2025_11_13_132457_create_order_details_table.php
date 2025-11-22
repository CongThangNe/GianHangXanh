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
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_variant_id')->constrained('product_variants');
    $table->integer('quantity');
    $table->decimal('price', 15, 2); // giá tại thời điểm mua
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
