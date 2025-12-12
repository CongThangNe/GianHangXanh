<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    // Chuyển logic từ vnpay_create_payment.php
    public function createPayment(Request $request)
    {
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURN_URL');

        // Lấy thông tin từ request (hoặc fix cứng cho test)
        $vnp_TxnRef = $request->input('order_id'); // Mã đơn hàng
        $vnp_Amount = $vnp_Amount = $request->input('amount'); // Số tiền (mặc định 10.000 VND)
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        // Cấu hình tham số gửi sang VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100, // VNPAY yêu cầu nhân 100
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan don hang: " . $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // ĐỂ HIỆN MÃ QR NGAY LẬP TỨC:
        // Set bankCode là VNPAYQR thì khi sang cổng thanh toán sẽ mở sẵn QR
        // $inputData['vnp_BankCode'] = 'VNPAYQR';

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng người dùng sang VNPAY
        return redirect($vnp_Url);
    }

    // Chuyển logic từ vnpay_return.php
    public function vnpayReturn(Request $request)
    {
        // 1. Lấy cấu hình
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $inputData = array();

        // 2. Lấy dữ liệu VNPAY trả về
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // 3. Kiểm tra tính toàn vẹn dữ liệu (Chữ ký số)
        if ($secureHash == $vnp_SecureHash) {

            // Lấy thông tin đơn hàng từ mã tham chiếu
            $orderId = $request->vnp_TxnRef;
            $order = Order::find($orderId);

            // Kiểm tra kết quả giao dịch (00 là thành công)
            if ($request->vnp_ResponseCode == '00') {

                if ($order) {
                    // CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
                    // Giả sử bạn có cột 'status' hoặc 'payment_status'
                    $order->status = 'processing'; // Hoặc 'paid', 'completed' tùy enum của bạn
                    $order->save();

                    return view('checkout.success', [
                        'order_code' => $order->order_code,
                        'amount' => $request->vnp_Amount / 100, // Chia 100 vì VNPAY nhân 100
                        'bank' => $request->vnp_BankCode,
                        'time' => $request->vnp_PayDate
                    ]);
                } else {
                    return "Đơn hàng không tồn tại!";
                }
            } else {
                // Thanh toán thất bại hoặc bị hủy
                return view('checkout.failed', ['msg' => 'Giao dịch bị hủy bỏ hoặc thất bại.']);
            }
        } else {
            return "Chữ ký không hợp lệ! (Có thể do sai HashSecret)";
        }
    }

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
            "bank_code"     => "zalopayapp",   //  Quan trọng: loại thanh toán ZaloPay App
            "embed_data"    => $embed_data,
            "item"          => $item,
            "callback_url"  => route('payment.zalopay.return'),
        ];

        // Tạo MAC
        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"] . "|" . $order["app_trans_id"] . "|" . $order["embed_data"] . "|" . $order["item"];
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
