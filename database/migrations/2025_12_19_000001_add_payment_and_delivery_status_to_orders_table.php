<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                // unpaid | paid | refunded (mở rộng sau)
                $table->string('payment_status')->default('unpaid')->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'delivery_status')) {
                // pending | confirmed | preparing | shipping | delivered | cancelled
                $table->string('delivery_status')->default('pending')->after('payment_status');
            }
        });

        // Backfill dữ liệu cũ từ cột status (nếu tồn tại)
        if (Schema::hasColumn('orders', 'status')) {
            DB::table('orders')->orderBy('id')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $payment = 'unpaid';
                    $delivery = 'pending';

                    $old = $row->status ?? null;

                    if ($old === 'paid') {
                        $payment = 'paid';
                        $delivery = 'pending';
                    } elseif (in_array($old, ['pending','confirmed','preparing','shipping','delivered','cancelled'], true)) {
                        $delivery = $old;
                        // Với dự án hiện tại, đơn online thường sẽ là paid sau khi gateway trả về.
                        // Đơn COD giữ unpaid cho tới khi bạn muốn xác nhận thu tiền.
                        $payment = $row->payment_method === 'cod' ? 'unpaid' : ($old === 'cancelled' ? 'unpaid' : 'paid');
                    }

                    DB::table('orders')->where('id', $row->id)->update([
                        'payment_status' => $payment,
                        'delivery_status' => $delivery,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_status')) {
                $table->dropColumn('delivery_status');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
