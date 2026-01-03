<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
public function createPayment(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id'
    ]);

    $order = Order::where('id', $request->order_id)
        ->where('payment_status', 'unpaid')
        ->firstOrFail();

    $vnp_TmnCode    = env('VNP_TMN_CODE');
    $vnp_HashSecret = env('VNP_HASH_SECRET');
    $vnp_Url        = env('VNP_URL');
    $vnp_ReturnUrl  = env('VNP_RETURN_URL');

    $vnp_Amount = (int) ($order->total * 100);
    if ($vnp_Amount <= 0) {
        abort(400, 'Số tiền không hợp lệ');
    }

    $inputData = [
        'vnp_Version'    => '2.1.0',
        'vnp_TmnCode'    => $vnp_TmnCode,
        'vnp_Amount'     => $vnp_Amount,
        'vnp_Command'    => 'pay',
        'vnp_CreateDate' => now()->format('YmdHis'),
        'vnp_CurrCode'   => 'VND',
        'vnp_IpAddr'     => $request->ip(),
        'vnp_Locale'     => 'vn',
        'vnp_OrderInfo'  => 'Thanh toan don ' . $order->order_code,
        'vnp_OrderType'  => 'other',
        'vnp_ReturnUrl'  => $vnp_ReturnUrl,
        'vnp_TxnRef'     => $order->order_code,
    ];

    ksort($inputData);

    // ✅ HASH DATA – URLENCODE
    $hashData = [];
    foreach ($inputData as $key => $value) {
        $hashData[] = $key . '=' . urlencode($value);
    }
    $hashData = implode('&', $hashData);

    $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    // ✅ QUERY STRING PHẢI GIỐNG HASH DATA
    $query = [];
    foreach ($inputData as $key => $value) {
        $query[] = $key . '=' . urlencode($value);
    }
    $queryString = implode('&', $query);

    $redirectUrl = $vnp_Url
        . '?' . $queryString
        . '&vnp_SecureHashType=HmacSHA512'
        . '&vnp_SecureHash=' . $vnp_SecureHash;

    return redirect()->away($redirectUrl);
}



   public function vnpayReturn(Request $request)
{
    $inputData = $request->all();
    $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

    unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
    ksort($inputData);

    $hashData = [];
    foreach ($inputData as $key => $value) {
        $hashData[] = $key . '=' . urlencode($value);
    }
    $hashData = implode('&', $hashData);

    $checkHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

    if ($checkHash !== $vnp_SecureHash) {
        return redirect()->route('home')->with('error', 'Sai chữ ký VNPay');
    }

    $order = Order::where('order_code', $request->vnp_TxnRef)->first();

    if (!$order) {
        return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
    }

    // ✅ THANH TOÁN THÀNH CÔNG
    if ($request->vnp_ResponseCode === '00') {
            DB::transaction(function () use ($order) {
        $order->update([
            'payment_status'  => 'paid',
            'delivery_status' => 'pending',
        ]);
        // 🔥 XÓA CART
        if ($order->session_id) {
            $cart = Cart::where('session_id', $order->session_id)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        } session()->forget(['discount_code', 'pending_discount']);
    });

        return redirect()
            ->route('home')
            ->with('success', "Thanh toán thành công {$order->order_code}");
    }

    // ❌ HỦY / FAIL / BACK
    $order->update([
        'payment_status'  => 'canceled',
        'delivery_status' => 'canceled',
    ]);

    return redirect()
        ->route('checkout.index')
        ->with('error', 'Bạn đã hủy thanh toán, đơn hàng đã bị hủy');
}


}
