<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if (isset($title))
    <title><?= $title ?></title>
    <meta property="og:title" content="<?= $title ?>">
    @else
    <title><?= config('videouri.default.title') ?></title>
    <meta property="og:title" content="<?= config('videouri.default.title') ?>">
    @endif

    @if (isset($description))
    <meta name="description" content="<?= $description ?>" />
    @else
    <meta name="description" content="<?= config('videouri.default.description') ?>" />
    @endif
  

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <meta property="og:site_name" content="Videouri"/>
    <meta property="og:url" content="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>"/>

    @if (isset($thumbnail))
    <meta property="og:type" content="video"/>
    <meta property="og:image" content="<?= $thumbnail ?>">
    @endif

    <link href="{{ secure_asset('/css/app.css') }}" rel="stylesheet">

    <meta name="msvalidate.01" content="48B0A933360DDEC6CF1775D7C7E28FD3" />

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Fredoka+One|Cabin&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
        
    @if (isset($canonical))
    <link rel="canonical" href="<?= url($canonical) ?>" />
    @endif

    @if (env('APP_ENV') !== 'local')
    <script type="text/javascript">
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-28752800-1', 'auto');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');
    </script>
    @endif
</head>
<body id="{{ isset($bodyId) ? $bodyId : '' }}">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-1 col-md-1 col-xs-4">
                    <a class="navbar-brand" href="<?= url('/'); ?>"> Videouri </a>
                </div>
                <div class="col-lg-6 col-md-6 col-md-offset-2 col-xs-7">
                    <form action="/results" class="navbar-form" role="search" method="get" autocomplete="off">
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
                <?php if (false): // @TODO ?>
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
                <?php endif ?>
            </div>
        </div>
    </nav>
    
    @yield('content')

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-sm-10">
                    <ul class="bottom-menu-list">
                        <li><a href="/info/legal/terms-of-use">Terms of Use</a></li>
                        <li><a href="/info/legal/dmca">DMCA</a></li>
                        <li>
                            <a href="//www.iubenda.com/privacy-policy/863528" class="iubenda-nostyle no-brand iubenda-embed" title="Privacy Policy">Privacy Policy</a>
                        </li>
                        @if (false)
                        <li>
                            <a href="" class="family-filter">
                                {{-- @todo --}}
                                Turn on / Turn off - family filter 
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-2 col-sm-2 text-right">
                    <ul class="bottom-menu-iconic-list">
                        <li><a href="https://facebook.com/Videouri" target="_blank" class="fa fa-facebook"></a></li>
                        <li><a href="https://twitter.com/Videouri" target="_blank" class="fa fa-twitter"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script type="text/javascript">
        (function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);
    </script>

    <!-- Scripts -->
    <script src="{{ secure_asset('/js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
