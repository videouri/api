@extends('app')

@section('content')
<br/>
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            {{-- URL is /history/videos => active --}}
            <li>
                <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'videos']) }}">
                    Watched videos
                </a>
            </li>
            <li>
                <a href="{{ route('user.{name}.history.show', ['name' => $currentUser, 'type' => 'search']) }}">
                    Search terms
                </a>
            </li>
        </ul>
    </div>
</div>
@endsection
