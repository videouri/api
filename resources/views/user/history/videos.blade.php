@extends('app')

@section('content')
    <div class="container">
        @include('user.history.menu')
        <videos-list content="videosWatched"></videos-list>
        {{-- <div class="row">
            <div class="col s12">
                <ul class="collection">
                    @foreach ($records as $record)
                    <li class="collection-item avatar">
                        <img src="{{ $record['thumbnail'] }}" alt="{{ $record['title'] }}" class="circle">
                        <span class="title">{{ $record['title'] }}</span
                    </li>
                    @endforeach
                </ul>
            </div>
        </div> --}}
    </div>
@endsection
