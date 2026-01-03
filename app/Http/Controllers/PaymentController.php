<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Cart;
use App\Models\ProductVariant;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->payment_status === 'paid') {
            abort(403, 'Đơn hàng đã thanh toán');
        }

        $inputData = [
            "vnp_Version"   => "2.1.0",
            "vnp_TmnCode"   => env('VNP_TMN_CODE'),
            "vnp_Amount"   => $order->total_amount * 100,
            "vnp_Command"  => "pay",
            "vnp_CreateDate"=> date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr"   => $request->ip(),
            "vnp_Locale"   => "vn",
            "vnp_OrderInfo"=> "Thanh toán đơn #" . $order->order_code,
            "vnp_OrderType"=> "other",
            "vnp_ReturnUrl"=> env('VNP_RETURN_URL'),
            "vnp_TxnRef"   => $order->id,
        ];

        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));
        $query    = http_build_query($inputData);

        $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

        return redirect(
            env('VNP_URL') . "?" . $query . "&vnp_SecureHash=" . $secureHash
        );
    }

    public function vnpayReturn(Request $request)
    {
        Log::info('VNPAY CALLBACK', $request->all());

        $inputData = [];
        foreach ($request->all() as $k => $v) {
            if (str_starts_with($k, 'vnp_')) {
                $inputData[$k] = $v;
            }
        }

        $secureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $checkHash = hash_hmac(
            'sha512',
            urldecode(http_build_query($inputData)),
            env('VNP_HASH_SECRET')
        );

        if ($checkHash !== $secureHash) {
            abort(403, 'Sai chữ ký VNPAY');
        }

        $order = Order::with('details')->findOrFail($request->vnp_TxnRef);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success');
        }

        DB::transaction(function () use ($order, $request) {

            if ($request->vnp_ResponseCode === '00') {

                $order->update([
                    'payment_status'  => 'paid',
                    'delivery_status' => 'pending',
                ]);

                Cart::where('session_id', session()->getId())->delete();
                session()->forget('discount_code');

            } else {

                foreach ($order->details as $detail) {
                    ProductVariant::where('id', $detail->product_variant_id)
                        ->increment('stock', $detail->quantity);
                }

                $order->update([
                    'payment_status'  => 'failed',
                    'delivery_status' => 'canceled',
                ]);
            }
        });

        return $request->vnp_ResponseCode === '00'
            ? view('checkout.success', [
                'order_code' => $order->order_code,
                'amount' => $request->vnp_Amount / 100
            ])
            : redirect()->route('checkout.index')
                ->with('error', 'Thanh toán thất bại hoặc bị hủy');
    }
}
