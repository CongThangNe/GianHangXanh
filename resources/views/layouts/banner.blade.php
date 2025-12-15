{{-- BANNER --}}
@if(isset($banners) && $banners->count())
<div id="homeBanner" class="carousel slide mb-4" data-bs-ride="carousel">

    <div class="carousel-inner">
        @foreach($banners as $key => $banner)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
            <img
                src="{{ Storage::url($banner->image) }}"
                class="d-block w-100"
                style="max-height:420px; object-fit:cover;"
                alt="{{ $banner->title }}"
            >
        </div>
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#homeBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#homeBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>
@endif
