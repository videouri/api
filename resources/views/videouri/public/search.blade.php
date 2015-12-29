@extends('app')

@section('content')
<div class="container">
    <videos-list filter_apis="enabled" content="search" query="{{ $query }}"></videos-list>
</div>
@endsection
