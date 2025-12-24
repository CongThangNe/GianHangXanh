@extends('layouts.admin')

@section('content')
<div class="container my-4">

    <h3 class="mb-4">📰 Tin tức</h3>

    <div class="row">
        @forelse($news as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">

                    @if($item->image)
                        <img src="{{ asset('storage/'.$item->image) }}"
                             class="card-img-top"
                             style="height:180px;object-fit:cover">
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            {{ $item->title }}
                        </h5>

                        <p class="card-text text-muted">
                            {{ Str::limit($item->short_description, 120) }}
                        </p>

                        <a href="{{ route('news.show', $item->id) }}"
                           class="btn btn-sm btn-primary mt-auto">
                            Xem chi tiết →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>Chưa có tin tức nào.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $news->links() }}
    </div>

</div>
@endsection
