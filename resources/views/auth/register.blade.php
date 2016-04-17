@extends('app')

@section('content')
<div class="container">
    <section id="register" class="row">
        <div class="col s6 push-s3">
            <h3 class="flow-text center-align">Register your new account</h3>
            @if (session('status'))
            <div class="card-panel yellow">
                <div class="flow-text">
                    {{ session('status') }}
                </div>
            </div>
            @endif

            @if (count($errors) > 0)
            <div class="card-panel red">
                <h4> Whoops! </h4>
                <p> There were some problems with your input. </p>

                <ul>
                    @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="z-depth-1 card-panel">
                <div class="row">
                    <div class="col s12">
                        <a href="/login/facebook" class="btn btn-block btn-flat facebook-bg text-center">
                            Register with Facebook
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <a href="/login/twitter" class="btn btn-block btn-flat twitter-bg text-center">
                            Register with Twitter
                        </a>
                    </div>
                </div>

                <div class="or-container">
                    <hr class="or-hr">
                    <div id="or">
                        or
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <form class="register-form" method="POST" action="{{ url('/register') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="row margin">
                                <div class="input-field col s12">
                                    <i class="mdi-social-person-outline prefix"></i>
                                    <input id="username" class="validate" required type="text" autocomplete="off" value="{{ old('username') }}" name="username" tabindex="1">
                                    <label for="username" class="center-align">Username</label>
                                </div>
                            </div>

                            <div class="row margin">
                                <div class="input-field col s12">
                                    <i class="mdi-social-person-outline prefix"></i>
                                    <input id="email" class="validate" required type="text" autocomplete="off" value="{{ old('email') }}" name="email" tabindex="2">
                                    <label for="email" class="center-align">Email</label>
                                </div>
                            </div>

                            <div class="row margin">
                                <div class="input-field col s12">
                                    <i class="mdi-action-lock-outline prefix"></i>
                                    <input id="password" class="validate" required type="password" name="password" tabindex="2">
                                    <label for="password" class="">Password</label>
                                </div>
                            </div>

                            <div class="row margin">
                                <div class="input-field col s12">
                                    <i class="mdi-action-lock-outline prefix"></i>
                                    <input id="password_confirmation" class="validate" required type="password" name="password_confirmation" tabindex="3">
                                    <label for="password_confirmation" class="">Password confirmation</label>
                                </div>
                            </div>

                            <button class="btn waves-effect waves-light" type="submit" name="action">
                                Register
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
