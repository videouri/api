@extends('app')

@section('content')
    <video-page :video="{{ $video }}" :user="{{ Auth::user() }}"></video-page>
@endsection
