@php
    // Banner slideshow (cứng) cho TRANG CHỦ.
    // Ảnh lấy từ public/uploads/banners (có sẵn trong project) để tránh phụ thuộc storage:link.
    $slides = [
        [
            'bg' => asset('uploads/banners/1767451739.jpg'),
            'title' => 'Chào mừng đến Gian Hàng Xanh 🌱',
            'desc' => 'Thực phẩm sạch - An toàn - Vì một tương lai xanh',
            'cta_text' => 'Khám phá ngay',
            'cta_link' => '#products',
        ],
        [
            'bg' => asset('uploads/banners/1767451752.jpg'),
            'title' => 'Ưu đãi mỗi ngày',
            'desc' => 'Săn deal xanh – tiết kiệm hơn, an tâm hơn',
            'cta_text' => 'Xem sản phẩm',
            'cta_link' => '#products',
        ],
        [
            'bg' => asset('uploads/banners/1767451777.jpg'),
            'title' => 'Shop theo danh mục',
            'desc' => 'Chọn nhanh theo nhu cầu của bạn',
            'cta_text' => 'Xem danh mục',
            'cta_link' => '#categories',
        ],
    ];
@endphp

<div id="homeBanner" class="mb-4 rounded-3 overflow-hidden relative" style="height: 250px;">
    @foreach($slides as $i => $s)
        <div class="home-slide absolute inset-0 bg-center bg-cover transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}"
             style="background-image: url('{{ $s['bg'] }}');">
        </div>
    @endforeach

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/35"></div>

    <!-- Nội dung theo slide -->
    <div class="absolute inset-0 flex flex-col justify-content-center items-center text-center px-3">
        @foreach($slides as $i => $s)
            <div class="home-slide-content transition-opacity duration-700 ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}">
                <h1 class="fw-bold" style="color: #ffffff; font-size: 1.8rem;">
                    {{ $s['title'] }}
                </h1>
                <p style="color: #ffffff; font-size: 1rem; margin-top: 6px;">
                    {{ $s['desc'] }}
                </p>
                <a href="{{ $s['cta_link'] }}" class="btn btn-success btn-sm mt-2">
                    {{ $s['cta_text'] }}
                </a>
            </div>
        @endforeach
    </div>

    <!-- Dots -->
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach($slides as $i => $_)
            <button type="button"
                    class="home-dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white/80 transition"
                    data-slide="{{ $i }}"
                    aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>

    <!-- Arrows -->
    <button type="button" id="homePrev"
            class="absolute left-3 top-1/2 -translate-y-1/2 z-10 h-9 w-9 rounded-full
                   bg-black/40 text-white hover:bg-black/60 transition flex items-center justify-center">
        ‹
    </button>
    <button type="button" id="homeNext"
            class="absolute right-3 top-1/2 -translate-y-1/2 z-10 h-9 w-9 rounded-full
                   bg-black/40 text-white hover:bg-black/60 transition flex items-center justify-center">
        ›
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.getElementById('homeBanner');
        if (!root) return;

        const slides = root.querySelectorAll('.home-slide');
        const contents = root.querySelectorAll('.home-slide-content');
        const dots = root.querySelectorAll('.home-dot');
        const prev = document.getElementById('homePrev');
        const next = document.getElementById('homeNext');

        let idx = 0;
        let timer = null;

        const setActiveDot = (i) => {
            dots.forEach((d, k) => {
                d.classList.toggle('bg-white/90', k === i);
                d.classList.toggle('bg-white/50', k !== i);
            });
        };

        const show = (i) => {
            idx = (i + slides.length) % slides.length;

            slides.forEach((el, k) => {
                el.classList.toggle('opacity-100', k === idx);
                el.classList.toggle('opacity-0', k !== idx);
            });

            contents.forEach((el, k) => {
                const active = k === idx;
                el.classList.toggle('opacity-100', active);
                el.classList.toggle('opacity-0', !active);
                el.classList.toggle('pointer-events-none', !active);
            });

            setActiveDot(idx);
        };

        const start = () => {
            stop();
            timer = setInterval(() => show(idx + 1), 4500);
        };

        const stop = () => {
            if (timer) clearInterval(timer);
            timer = null;
        };

        dots.forEach(d => d.addEventListener('click', () => {
            const target = parseInt(d.dataset.slide, 10);
            if (!Number.isFinite(target)) return;
            show(target);
            start();
        }));

        prev?.addEventListener('click', () => {
            show(idx - 1);
            start();
        });

        next?.addEventListener('click', () => {
            show(idx + 1);
            start();
        });

        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);

        // init
        show(0);
        start();
    });
</script>
