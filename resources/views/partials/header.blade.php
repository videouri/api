<header>
    <ul id="user-menu" class="dropdown-content">
        <li>
            <a href="{{ url('/logout') }}">Logout</a>
        </li>
    </ul>
    <nav id="top-nav">
        <div class="container">
            <div class="nav-wrapper">
                <div class="row">
                    <div class="col s1 hide-on-large-only">
                        <a href="#" data-activates="nav-mobile"
                           class="button-collapse top-nav full black-text">
                            <i class="material-icons">menu</i>
                        </a>
                    </div>
                    <form action="/search" method="get" id="navbar-search" class="col s5 m7 offset-s2"
                          autocomplete="off">
                        <div class="row">
                            <div class="input-field col s10">
                                <input id="search" type="text" name="query" value="{{ isset($query) ? $query : '' }}"
                                       class="validate" required placeholder="Search">
                            </div>
                            <div class="input-field col s2">
                                <button id="submit-search" class="btn waves-effect waves-light" type="submit">
                                    <i class="material-icons">search</i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="col s4 m4 right hide-on-med-and-down">
                        <div id="login-signup" class="right-nav right-align">
                            @if (Auth::guest())
                                <div class="row">
                                    <div class="col s6 right-align">
                                        <a href="{{ url('login') }}" class="waves-effect waves-light btn-flat login"
                                           id="signup-navbar">
                                            login
                                        </a>
                                    </div>
                                    <div class="col s6">
                                        <a href="{{ url('register') }}" class="waves-effect waves-light btn white-text">
                                            sign up
                                        </a>
                                    </div>
                                </div>
                            @else
                                <a href="#!" class="dropdown-button" data-activates="user-menu">
                                    {{ Auth::user()->username }}
                                    <i class="fa fa-angle-down" style="display: inherit; font-size: inherit;"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @include('partials.sidebar')
</header>
