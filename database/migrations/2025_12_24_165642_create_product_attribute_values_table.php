<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_value_id');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('attribute_value_id')
                ->references('id')->on('attribute_values')
                ->onDelete('cascade');

            $table->unique(['product_id', 'attribute_value_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attribute_values');
    }
};

