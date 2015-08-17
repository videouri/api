@extends('app')

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        @if (session('status'))
        <div class="tile tile-padded alert">
            <div class="alert-info">
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
            <a href="/login/twitter" class="btn btn-block btn-social-twitter text-center">
                <i class="fa fa-twitter-square"></i>
                &nbsp; Register with Twitter
            </a>

            <a href="/login/facebook" class="btn btn-block btn-social-facebook text-center">
                <i class="fa fa-facebook-square"></i>
                &nbsp; Register with Facebook
            </a>

            <div class="or-container">
                <hr class="or-hr">
                <div id="or">
                    or
                </div>
            </div>

            <form class="form" role="form" method="POST" action="{{ url('/join') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Enter your username" tabindex="1">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-user"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your email" tabindex="2">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-envelope"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" placeholder="Choose a password" tabindex="3">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-lock"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm your password" tabindex="4">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-lock"></i></button>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Register
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
