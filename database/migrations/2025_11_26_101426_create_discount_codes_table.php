<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('discount_codes', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();

        // 2 kiểu giảm giá: theo % hoặc theo số tiền
        $table->integer('discount_percent')->default(0); // Nếu =0 thì ko dùng %
        $table->integer('discount_value')->default(0);   // Nếu =0 thì ko dùng giảm tiền cố định

        // Giới hạn
        $table->integer('max_uses')->default(0);         // 0 = không giới hạn
        $table->integer('used_count')->default(0);

        // Thời gian áp dụng
        $table->date('starts_at')->nullable();
        $table->date('expires_at')->nullable();

        // Nếu giảm theo % thì cần giới hạn mức giảm tối đa
        $table->integer('max_discount_value')->default(0);

        $table->boolean('active')->default(true);

        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
