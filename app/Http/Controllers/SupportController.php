<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index');
    }

    public function store(Request $request)
    {
        // Normalize phone: bỏ khoảng trắng / dấu gạch / ngoặc để user nhập "0912 345 678" vẫn ok
        $phoneRaw = (string) $request->input('phone', '');
        $phoneNormalized = preg_replace('/[\s\-\(\)]/', '', $phoneRaw);
        $request->merge(['phone' => $phoneNormalized]);

        $allowedSubjects = ['Đơn hàng', 'Thanh toán', 'Giao hàng', 'Đổi trả', 'Sản phẩm', 'Khác'];

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            // VN phone: 0 + 9 số  => 10 số, hoặc +84 + 9 số
            'phone'   => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9}$/'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', Rule::in($allowedSubjects)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required'     => 'Vui lòng nhập họ tên.',
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'subject.required'  => 'Vui lòng chọn chủ đề.',
            'subject.in'        => 'Chủ đề không hợp lệ.',
            'message.required'  => 'Vui lòng nhập nội dung.',
            'message.min'       => 'Nội dung tối thiểu 10 ký tự.',
            'message.max'       => 'Nội dung tối đa 5000 ký tự.',
            'phone.required'    => 'Vui lòng nhập số điện thoại.',
            'phone.regex'       => 'Số điện thoại Việt Nam không hợp lệ (VD: 0912345678 hoặc +84912345678).',
        ]);

        SupportRequest::create($validated + ['status' => 'new']);

        return redirect()
            ->route('support.index')
            ->with('success', 'Đã gửi yêu cầu hỗ trợ. Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }
}
