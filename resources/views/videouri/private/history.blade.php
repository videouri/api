@extends('app')

@section('content')
<br/>
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            <li class="@if (Route::is('videos-history')) active @endif">
                <a href="{{ route('videos-history') }}">Watched videos</a>
            </li>
            <li>
                <a href="/history/search">Search terms</a>
            </li>
        </ul>
    </div>
</div>
@endsection
