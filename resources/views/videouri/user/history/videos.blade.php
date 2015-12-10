@extends('app')

@section('content')
<div class="container">
    @include('videouri.user.history.menu')
    <div class="row">
        <div class="col s12">
            <ul class="collection">
            @foreach ($records as $record)
                <li class="collection-item avatar">
                    <img src="{{ $record->video->thumbnail }}" alt="">
                    <span class="title">{{ $record->video->title }}</span
                </li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
