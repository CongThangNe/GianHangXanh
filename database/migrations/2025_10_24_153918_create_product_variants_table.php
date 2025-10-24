<?php

use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class CreateProductVariantsTable extends Migration
   {
       public function up()
       {
           if (!Schema::hasTable('product_variants')) {
               Schema::create('product_variants', function (Blueprint $table) {
                   $table->id();
                   $table->unsignedBigInteger('product_id');
                   $table->string('name')->nullable();
                   $table->decimal('price', 8, 2)->nullable();
                   $table->timestamps();

                   $table->foreign('product_id')
                         ->references('id')
                         ->on('products')
                         ->onDelete('cascade');
               });
           }
       }

       public function down()
       {
           Schema::dropIfExists('product_variants');
       }
   }
