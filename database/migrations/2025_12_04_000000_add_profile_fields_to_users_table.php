<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thông tin hồ sơ cơ bản cho người dùng
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            // Lưu đường dẫn avatar trong storage (disk: public)
            $table->string('avatar_path')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'avatar_path']);
        });
    }
};
