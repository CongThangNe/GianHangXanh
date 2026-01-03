<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('discount_codes', function (Blueprint $table) {
    $table->id();

    $table->string('code')->unique();
    $table->enum('type', ['percent', 'value']);

    $table->unsignedInteger('value'); 

    $table->unsignedInteger('max_discount_value')->nullable();
    $table->unsignedInteger('max_uses')->default(0);
    $table->unsignedInteger('used_count')->default(0);

    $table->timestamp('starts_at')->nullable();
    $table->timestamp('expires_at')->nullable();

    $table->boolean('active')->default(true);

    $table->timestamps();
});


    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
