@extends('app')

@section('content')
    <div class="container">
        <section id="reset-password" class="row">
            <div class="col s6 push-s3">
                <h3 class="flow-text center-align">
                    Request reset password link
                </h3>
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


                <div class="z-depth-1 card-panel padded-panel">
                    <div class="row">
                        <div class="col s12">
                            <form class="form" role="form" method="POST" action="{{ url('/password/email') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="row margin">
                                    <div class="input-field col s12">
                                        <input type="email" class="validate" name="email" value="{{ old('email') }}" tabindex="1" required>
                                        <label for="email">E-Mail Address</label>
                                    </div>
                                </div>

                                <button class="btn waves-effect waves-light" type="submit" name="action">
                                    Send Password Reset Link
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
