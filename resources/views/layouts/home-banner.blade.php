@php
    // Banner slideshow (cứng) cho TRANG CHỦ.
    // Ảnh lấy từ public/uploads/banners để FE tự thay ảnh mà không cần BE/DB.
    $slides = [
        asset('uploads/banners/1767451739.jpg'),
        asset('uploads/banners/1767451752.jpg'),
        asset('uploads/banners/1767451777.jpg'),
    ];
@endphp

<div id="homeBanner"
     class="mb-4 rounded-3 overflow-hidden relative bg-[#0b3d1f]"
     style="height: 360px;">

    @foreach($slides as $i => $bg)
        <div class="home-slide absolute inset-0 flex items-center justify-center transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}">
            <img src="{{ $bg }}"
                 alt="Banner {{ $i + 1 }}"
                 class="w-full h-full object-contain select-none pointer-events-none" />
        </div>
    @endforeach

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('homeBanner');
    if (!root) return;

    const slides = root.querySelectorAll('.home-slide');
    if (!slides.length) return;

    let idx = 0;

    const show = (i) => {
        idx = (i + slides.length) % slides.length;

        slides.forEach((el, k) => {
            el.classList.toggle('opacity-100', k === idx);
            el.classList.toggle('opacity-0', k !== idx);
        });
    };

    // Init
    show(0);

    // Auto play
    setInterval(() => show(idx + 1), 4500);
});
</script>
