<header>
    <nav id="top-nav">
        <div class="container">
            <div class="nav-wrapper">
                <div class="row">
                    <div class="col s1">
                        <a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only black-text">
                            <i class="mdi-navigation-menu"></i>
                        </a>
                    </div>
                    <form action="/results" method="get" id="navbar-search" class="col s5 m8 offset-s2" autocomplete="off">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="search" type="text" name="search_query" value="{{ isset($searchQuery) ? $searchQuery : '' }}" class="validate" required placeholder="Search">
                                {{-- <i class="material-icons prefix">search</i> --}}
                                {{-- <label for="search">Search...</label> --}}
                            </div>
                        </div>
                    </form>
                    <div class="col s4 m4">
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
                                <ul class="">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->username }} <span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="{{ url('/profile') }}">Profile</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/settings') }}">Settings</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="{{ url('/logout') }}">Logout</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <ul id="nav-mobile" class="side-nav fixed">
        <li class="logo">
            <a id="logo-container" href="{{ url('/') }}" class="brand-logo">
                <span class="videouri">
                    Videouri
                </span>
                <!-- <object id="front-page-logo" type="image/svg+xml" data="res/materialize.svg">Your browser does not support SVG</object> -->
            </a>
        </li>

        <li class="bold"><a href="about.html" class="waves-effect waves-teal">About</a></li>
        <li class="bold"><a href="getting-started.html" class="waves-effect waves-teal">Getting Started</a></li>

        <li class="nav-header"> Explore </li>
        <li class="@if (Route::is('home')) active @endif">
            <a href="{{ route('home') }}">
                <i class="fa fa-home"></i>
                What to watch
            </a>
        </li>

        @if ($currentUser !== 'guest')
        <li class="@if (Route::is('user-favorites')) active @endif">
            <a href="{{ route('user.{name}.favorites.index', ['name' => $currentUser]) }}">
                <i class="fa fa-star"></i>
                Favorites
            </a>
        </li>
        <li class="@if (Route::is('user-history')) active @endif">
            <a href="{{ route('user.{name}.history.index', ['name' => 1]) }}">
                <i class="fa fa-history"></i>
                History
            </a>
        </li>
        @endif

        @if (false)
        <li class="divider"></li>

        <li class="nav-header"> Filters </li>
        <li>
            <a href="#">Overview <span class="sr-only">(current)</span></a>
        </li>
        <li><a href="#">Reports</a></li>
        <li><a href="#">Analytics</a></li>
        <li><a href="#">Export</a></li>
        @endif
    </ul>
</header>
<!-- <div class="navbar navbar-fixed">
    <nav id="header-nav" role="navigation">
        <div class="nav-wrapper">
            <div class="row">
                <div class="col s2 center">
                    <a class="navbar-brand" href="<?= url('/'); ?>"> Videouri </a>
                </div>
                <div class="col s8">

                </div>
                <div class="col s2 right">
                    <ul class="nav navbar-nav navbar-right text-right">
                        @if (Auth::guest())
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->username }} <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ url('/profile') }}">Profile</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('/settings') }}">Settings</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ url('/logout') }}">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>
-->
