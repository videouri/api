@extends('app')

@section('content')
<div class="container">
    <videos-list content="search" query="{{ $query }}"></videos-list>
</div>
@endsection
