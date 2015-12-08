<header>
    <ul id="user-menu" class="dropdown-content">
        <li>
            <a href="{{ route('user.{name}.profile.index', ['name' => $currentUser]) }}">Profile</a>
        </li>
        <li>
            <a href="{{ route('user.{name}.settings.index', ['name' => $currentUser]) }}">Settings</a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="{{ url('/logout') }}">Logout</a>
        </li>
    </ul>
    <nav id="top-nav">
        <div class="container">
            <div class="nav-wrapper">
                <div class="row">
                    <div class="col s1">
                        <a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only black-text">
                            <i class="mdi-navigation-menu"></i>
                        </a>
                    </div>
                    <form action="/search" method="get" id="navbar-search" class="col s5 m7 offset-s2" autocomplete="off">
                        <div class="row">
                            <div class="input-field col s11">
                                <input id="search" type="text" name="query" value="{{ isset($query) ? $query : '' }}" class="validate" required placeholder="Search">
                            </div>
                            <div class="input-field col s1">
                                <button id="submit-search" class="btn waves-effect waves-light" type="submit">
                                    <i class="mdi-action-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="col s4 m4 right">
                        <div id="login-signup" class="right-nav hide-on-med-and-down right-align">
                            @if (Auth::guest())
                                <div class="row">
                                    <div class="col s6 right-align">
                                        <a href="{{ route('login') }}" class="waves-effect waves-light btn-flat login" id="signup-navbar">
                                            login
                                        </a>
                                    </div>
                                    <div class="col s6">
                                        <a href="{{ route('register') }}" class="waves-effect waves-light btn white-text">
                                            sign up
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{-- <a href="{{ route('register') }}" class="waves-effect waves-light btn white-text">
                                    sign up
                                </a> --}}
                                <a href="#!" class="dropdown-button" data-activates="user-menu">
                                    {{ Auth::user()->username }}
                                    <i class="material-icons right">arrow_drop_down</i>
                                </a>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @include('videouri.partials.sidebar')
</header>
