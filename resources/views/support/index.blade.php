@extends('layouts.app')
@section('title', 'Liên hệ & Hỗ trợ')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex flex-col gap-2 mb-6">
        <h1 class="text-2xl md:text-3xl font-bold">Liên hệ & Hỗ trợ</h1>
        <p class="text-sm text-subtle-light dark:text-subtle-dark">
            Nếu bạn cần hỗ trợ về đơn hàng, thanh toán hoặc sản phẩm, hãy chọn kênh liên hệ nhanh hoặc gửi yêu cầu qua form bên dưới.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contact cards -->
        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark p-5">
                <h2 class="font-bold text-lg mb-3">Liên hệ nhanh</h2>

                <div class="flex flex-col gap-3 text-sm">
                    <div>
                        <div class="font-semibold">Hotline</div>
                        <div class="text-subtle-light dark:text-subtle-dark">0900 000 000</div>
                    </div>
                    <div>
                        <div class="font-semibold">Email</div>
                        <div class="text-subtle-light dark:text-subtle-dark">support@gianhangxanh.local</div>
                    </div>
                    <div>
                        <div class="font-semibold">Zalo</div>
                        <div class="text-subtle-light dark:text-subtle-dark">Gian Hàng Xanh</div>
                    </div>
                    <div>
                        <div class="font-semibold">Giờ làm việc</div>
                        <div class="text-subtle-light dark:text-subtle-dark">08:00 – 20:00 (Thứ 2 – CN)</div>
                    </div>
                    <div class="pt-2 text-xs text-subtle-light dark:text-subtle-dark">
                        Thời gian phản hồi dự kiến: trong 24 giờ (giờ hành chính).
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark p-5">
                <h2 class="font-bold text-lg mb-3">Câu hỏi thường gặp</h2>

                <div class="flex flex-col gap-3 text-sm">
                    <details class="rounded-lg border border-border-light dark:border-border-dark p-3">
                        <summary class="cursor-pointer font-semibold">1) Tôi theo dõi đơn hàng ở đâu?</summary>
                        <div class="mt-2 text-subtle-light dark:text-subtle-dark">
                            Bạn có thể xem trạng thái đơn trong mục Đơn hàng (nếu đã đăng nhập) hoặc liên hệ hotline để được hỗ trợ.
                        </div>
                    </details>

                    <details class="rounded-lg border border-border-light dark:border-border-dark p-3">
                        <summary class="cursor-pointer font-semibold">2) Tôi có thể đổi trả sản phẩm không?</summary>
                        <div class="mt-2 text-subtle-light dark:text-subtle-dark">
                            Vui lòng giữ hóa đơn và liên hệ trong 24–48 giờ kể từ khi nhận hàng (tuỳ loại sản phẩm).
                        </div>
                    </details>

                    <details class="rounded-lg border border-border-light dark:border-border-dark p-3">
                        <summary class="cursor-pointer font-semibold">3) Phí giao hàng được tính như thế nào?</summary>
                        <div class="mt-2 text-subtle-light dark:text-subtle-dark">
                            Phí giao hàng phụ thuộc khu vực và đơn vị vận chuyển. Hệ thống sẽ hiển thị ở bước thanh toán.
                        </div>
                    </details>

                    <details class="rounded-lg border border-border-light dark:border-border-dark p-3">
                        <summary class="cursor-pointer font-semibold">4) Tôi gặp lỗi thanh toán thì làm gì?</summary>
                        <div class="mt-2 text-subtle-light dark:text-subtle-dark">
                            Chụp màn hình lỗi và gửi qua form bên cạnh hoặc liên hệ hotline để được hỗ trợ nhanh.
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <!-- Support form -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark p-6">
                <h2 class="font-bold text-lg mb-4">Gửi yêu cầu hỗ trợ</h2>

                <form method="POST" action="{{ route('support.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-semibold">Họ tên <span class="text-red-500">*</span></label>
                            <input name="name" value="{{ old('name') }}" required maxlength="255"
                                   class="mt-1 w-full rounded-lg border border-border-light dark:border-border-dark bg-transparent px-3 py-2"
                                   placeholder="Nhập họ tên">
                            @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold">Số điện thoại <span class="text-red-500">*</span></label>
                            <input name="phone" value="{{ old('phone') }}" required maxlength="20"
                                   inputmode="tel"
                                   placeholder="VD: 0912345678 hoặc +84912345678"
                                   class="mt-1 w-full rounded-lg border border-border-light dark:border-border-dark bg-transparent px-3 py-2">
                            <div class="text-xs text-subtle-light dark:text-subtle-dark mt-1">
                                Hỗ trợ nhập có khoảng trắng/dấu gạch (hệ thống sẽ tự chuẩn hoá).
                            </div>
                            @error('phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold">Email <span class="text-red-500">*</span></label>
                            <input name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                                   class="mt-1 w-full rounded-lg border border-border-light dark:border-border-dark bg-transparent px-3 py-2"
                                   placeholder="VD: email@domain.com">
                            @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold">Chủ đề <span class="text-red-500">*</span></label>
                            <select name="subject" required
                                    class="mt-1 w-full rounded-lg border border-border-light dark:border-border-dark bg-transparent px-3 py-2">
                                <option value="" disabled @selected(old('subject') === null || old('subject') === '')>Chọn chủ đề</option>
                                @php $subjects = ['Đơn hàng','Thanh toán','Giao hàng','Đổi trả','Sản phẩm','Khác']; @endphp
                                @foreach($subjects as $s)
                                    <option value="{{ $s }}" @selected(old('subject') === $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('subject')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold">Nội dung <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="6" required minlength="10" maxlength="5000"
                                  class="mt-1 w-full rounded-lg border border-border-light dark:border-border-dark bg-transparent px-3 py-2"
                                  placeholder="Mô tả vấn đề bạn gặp phải...">{{ old('message') }}</textarea>
                        @error('message')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="rounded-lg h-10 px-5 bg-primary/20 dark:bg-primary/30 text-sm font-bold
                                       hover:bg-primary/30 dark:hover:bg-primary/40 transition">
                            Gửi yêu cầu
                        </button>
                        <div class="text-xs text-subtle-light dark:text-subtle-dark">
                            Bằng việc gửi, bạn đồng ý để chúng tôi liên hệ lại qua thông tin bạn cung cấp.
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-6 rounded-xl border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark p-6">
                <h2 class="font-bold text-lg mb-2">Gợi ý nhanh</h2>
                <ul class="list-disc pl-5 text-sm text-subtle-light dark:text-subtle-dark space-y-1">
                    <li>Nếu liên quan thanh toán: gửi kèm thời gian và hình ảnh lỗi (nếu có).</li>
                    <li>Nếu liên quan đơn hàng: ghi rõ mã đơn hoặc số điện thoại đặt hàng.</li>
                    <li>Nếu liên quan đổi trả: chụp ảnh sản phẩm và tình trạng tem/nhãn.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
