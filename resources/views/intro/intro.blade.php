@extends('layouts.app')
@section('title', 'Giới Thiệu - Gian Hàng Xanh')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 max-w-6xl">

    <div class="bg-white shadow-lg rounded-lg p-6 md:p-10 mt-10">
        <h1 class="text-4xl font-bold text-green-600 text-center">🌿 Gian Hàng Xanh 🌿</h1>
        <p class="text-gray-700 text-center mt-2">Chuyên cung cấp sản phẩm xanh, an toàn và thân thiện với môi trường</p>

        <div class="mt-6">
            <p class="text-gray-700 leading-relaxed">
                GIAN HÀNG XANH được thành lập với sứ mệnh mang đến cho khách hàng những sản phẩm sạch, hữu cơ và thân thiện với môi trường. Chúng tôi cam kết đảm bảo chất lượng từ nguồn nguyên liệu, quy trình sản xuất và đóng gói để bạn an tâm sử dụng.
            </p>
            <p class="mt-4 font-semibold text-green-700">
                Hãy nhận diện logo màu xanh lá chính hãng để đảm bảo mua sản phẩm chất lượng, an toàn và bền vững.
            </p>
        </div>

        <h2 class="text-2xl font-bold text-green-600 mt-6">📍 Hệ thống cửa hàng toàn quốc</h2>

        <div class="mt-4">
            <h3 class="text-xl font-semibold text-green-700">🏢 Tại Hà Nội:</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                <li>13 Trịnh Văn Bô - Hotline: <span class="font-bold">087.8888.900</span></li>
                <li>88 Đường Láng – Q.Đống Đa - Hotline: <span class="font-bold">087.8888.900</span></li>
                <li>58 Xuân Thủy – P.Dịch Vọng – Q.Cầu Giấy - Hotline: <span class="font-bold">087.8888.900</span></li>
            </ul>
        </div>

        <div class="mt-4">
            <h3 class="text-xl font-semibold text-green-700">🏢 Tại Hồ Chí Minh:</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                <li>228 Âu Cơ, Phường 9, Tân Bình - Hotline: <span class="font-bold">09.6618.6622</span></li>
                <li>99 Bàu Cát, Phường 14, Tân Bình - Hotline: <span class="font-bold">09.6618.6622</span></li>
                <li>590 Quang Trung, Phường 10, Gò Vấp - Hotline: <span class="font-bold">037.838.6622</span></li>
            </ul>
        </div>

        <p class="mt-6 text-gray-700 font-semibold">
            🔗 Website chính thức: 
            <a href="{{ url('/') }}" class="text-green-500 underline">https://gianhangxanh.vn</a>
        </p>

        <p class="mt-6 text-gray-600 italic">Chỉ mua hàng từ website chính thức để đảm bảo chất lượng, an toàn sức khỏe và thân thiện môi trường!</p>
    </div>

</div>
@endsection
