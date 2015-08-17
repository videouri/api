@extends('app')

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        @if (session('status'))
        <div class="tile tile-padded alert alert-info">
            {{ session('status') }}
        </div>
        @endif

        @if (count($errors) > 0)
        <div class="tile tile-padded alert alert-danger">
            <h4> Whoops! </h4>
            <p> There were some problems with your input. </p>
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
        @endif

        <div class="tile tile-padded">
            <a href="/login/twitter" class="btn btn-block btn-social-twitter text-center">
                <i class="fa fa-twitter-square"></i>
                &nbsp; Login with Twitter
            </a>

            <a href="/login/facebook" class="btn btn-block btn-social-facebook text-center">
                <i class="fa fa-facebook-square"></i>
                &nbsp; Login with Facebook
            </a>

            <div class="or-container">
                <hr class="or-hr">
                <div id="or">
                    or
                </div>
            </div>

            <form class="form" id="login" role="form" method="POST" action="{{ url('/login') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter your email" tabindex="1">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-user"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" tabindex="2">
                        <span class="input-group-btn">
                            <button type="button" class="btn"><i class="fa fa-lock"></i></button>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" tabindex="3"> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 pull-right">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>

                <div class="form-group">
                    <a class="btn btn-link" href="{{ url('/password/email') }}">
                        Forgot Your Password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
