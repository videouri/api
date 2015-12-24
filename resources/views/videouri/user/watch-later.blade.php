@extends('app')

@section('content')
<div class="container">
    <h1 class="flow-text">Your videos pending to be watched</h1>
    <videos-list content="watchLater"></videos-list>
</div>
@endsection
