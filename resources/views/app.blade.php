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

    {{-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"> --}}
    <link href="{{ videouri_asset('/css/app.css') }}" rel="stylesheet">

    <meta name="msvalidate.01" content="48B0A933360DDEC6CF1775D7C7E28FD3" />

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
<body id="app" class="{{ isset($bodyId) ? $bodyId : '' }}">
    @include('videouri.partials.header')

    <main>
        @yield('content')
    </main>

    <!-- <footer class="footer">
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
    </footer> -->

    <script type="text/javascript">
        (function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);
    </script>

    <!-- Scripts -->
    <script src="{{ videouri_asset('/js/vendor.js') }}"></script>
    <script src="{{ videouri_asset('/js/app.js') }}"></script>
    @yield('scripts')

    @if (Config::get('app.debug'))
    <script type="text/javascript">
        document.write('<script src="http://localhost:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
    </script>
    @endif
</body>
</html>
