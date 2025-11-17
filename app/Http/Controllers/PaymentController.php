<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function zaloPayApp(Request $request)
    {
        $endpoint = env('ZALOPAY_ENDPOINT');
        $app_id   = env('ZALOPAY_APP_ID');
        $key1     = env('ZALOPAY_KEY1');

        // Mã đơn (bắt buộc format yymmdd_xxxxx)
        $transID = time();
        $app_trans_id = date("ymd") . "_" . $transID;

        // Tổng tiền cần thanh toán (bạn thay bằng tổng giỏ hàng)
        $amount = 10000;

        // Embed data (dùng để nhận dữ liệu thêm)
        $embed_data = json_encode([]);

        // Danh sách sản phẩm khi cần gửi sang ZaloPay
        $item = json_encode([]);

        $order = [
            "app_id"        => $app_id,
            "app_trans_id"  => $app_trans_id,
            "app_user"      => "user@example.com", 
            "amount"        => $amount,
            "description"   => "Thanh toán qua ZaloPay App #" . $transID,
            "bank_code"     => "zalopayapp",   // 🔥 Quan trọng: loại thanh toán ZaloPay App
            "embed_data"    => $embed_data,
            "item"          => $item,
            "callback_url"  => route('payment.zalopay.return'),
        ];

        // Tạo MAC
        $data = $order["app_id"] ."|". $order["app_trans_id"] ."|". $order["app_user"] ."|". $order["amount"] ."|". $order["app_trans_id"] ."|". $order["embed_data"] ."|". $order["item"];
        $order["mac"] = hash_hmac("sha256", $data, $key1);

        // Gửi request sang ZaloPay
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if (!empty($response['order_url'])) {
            // Redirect sang app ZaloPay
            return redirect($response['order_url']);
        }

        return "Có lỗi khi kết nối ZaloPay!";
    }

    public function zaloReturn(Request $request)
    {
        if ($request->input('status') == 1) {
            return "Thanh toán ZaloPay App THÀNH CÔNG!";
        } else {
            return "Thanh toán thất bại hoặc bị hủy!";
        }
    }
}
