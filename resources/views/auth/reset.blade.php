@extends('app')

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        @if (count($errors) > 0)
        <div class="tile tile-padded alert alert-danger">
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
            <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 30px">Reset your password</h2>
            <form class="form" role="form" method="POST" action="{{ url('/password/reset') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-envelope"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" placeholder="Choose a password">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-lock"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm your password">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-lock"></i></button>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
