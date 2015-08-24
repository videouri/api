<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-1 col-md-1 col-xs-4">
                <a class="navbar-brand" href="<?= url('/'); ?>"> Videouri </a>
            </div>
            <div class="col-lg-6 col-md-6 col-md-offset-2 col-xs-7">
                <form action="/results" class="navbar-form" role="search" method="get" autocomplete="off">
                    <input type="hidden" name="_method" value="GET">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control" type="text" name="search_query" placeholder="Search"
                                    value="<?= isset($searchQuery) ? $searchQuery : '' ?>">
                            <span class="input-group-btn">
                                <?php if (false): // @TODO ?>
                                <!-- <button class="btn" data-toggle="dropdown">
                                    <i class="fa fa-filter"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-inverse" role="menu">
                                    <li><a href="#">Action</a></li>
                                    <li><a href="#">Another action</a></li>
                                    <li><a href="#">Something else here</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Separated link</a></li>
                                </ul> -->
                                <?php endif ?>
                                <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::guest())
                        <li><a href="{{ url('/join') }}" class="join">Join</a></li>
                        <li><a href="{{ url('/login') }}">Login</a></li>
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