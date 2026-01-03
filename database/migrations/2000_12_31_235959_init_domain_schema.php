<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============= CATEGORIES =============
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // ============= PRODUCTS =============
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0); // đồng bộ với ProductController
            $table->integer('stock')->default(0);
            $table->string('image')->nullable(); // lưu basename như mày đang làm
            $table->timestamps();
        });

        // ============= ATTRIBUTES =============
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // ============= ATTRIBUTE VALUES =============
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('value');
            $table->timestamps();
        });

        // ============= PRODUCT VARIANTS =============
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->unsignedBigInteger('price'); // giá theo biến thể
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // ============= PRODUCT VARIANT VALUES (pivot) =============
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
            $table->unique(['product_variant_id', 'attribute_value_id'], 'pv_attr_unique');
        });

        // ============= CARTS =============
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });


        // ============= CART ITEMS =============
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()
                ->constrained('product_variants')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('price'); // snapshot giá tại thời điểm thêm vào giỏ
            $table->timestamps();
        });

        // ============= ORDERS =============
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('total')->default(0); // tổng tiền đơn
            $table->string('payment_method'); // cod | zalopay
            $table->string('status')->default('pending'); // pending | paid | cancelled

            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->text('note')->nullable();

            $table->timestamps();
        });

        // ============= ORDER DETAILS =============
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('price'); // đơn giá tại thời điểm mua
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_variant_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
