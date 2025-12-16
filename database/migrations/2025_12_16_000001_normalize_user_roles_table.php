<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Chuẩn hoá dữ liệu cũ: 'user' -> 'customer'
        DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
    }

    public function down(): void
    {
        // Rollback: customer -> user
        DB::table('users')->where('role', 'customer')->update(['role' => 'user']);
    }
};
