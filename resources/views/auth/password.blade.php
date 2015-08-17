@extends('app')

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        @if (session('status'))
        <div class="tile tile-padded alert">
            <div class="alert-success">
                {{ session('status') }}
            </div>
        </div>
        @endif

        @if (count($errors) > 0)
        <div class="tile tile-padded alert ">
            <h4> Whoops! </h4>
            <p> There were some problems with your input. </p>
            @foreach ($errors->all() as $error)
                <div class="alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        </div>
        @endif

        <div class="tile tile-padded">
            <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 30px">Request reset password link</h2>
            <form class="form" role="form" method="POST" action="{{ url('/password/email') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-envelope"></i></button>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Send Password Reset Link
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
