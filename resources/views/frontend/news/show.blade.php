@extends('layouts.admin')

@section('content')
<div class="container my-4">

    <h2>{{ $news->title }}</h2>

    <p class="text-muted">
        🕒 {{ $news->created_at->format('d/m/Y') }}
    </p>

    @if($news->image)
        <img src="{{ asset('storage/'.$news->image) }}"
             class="img-fluid mb-4">
    @endif

    <div class="content">
        {!! $news->content !!}
    </div>

</div>
@endsection
