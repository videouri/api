<!DOCTYPE html>
<html lang="en-us">
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
        <meta name="description" content="<?= $description ?>"/>
    @else
        <meta name="description" content="<?= config('videouri.default.description') ?>"/>
    @endif

    <link rel="alternate" href="https://videouri.com" hreflang="en-us"/>

    <meta property="og:site_name" content="Videouri"/>
    <meta property="og:url" content=""/>

    @if (isset($thumbnail))
        <meta property="og:type" content="video"/>
        <meta property="og:image" content="<?= $thumbnail ?>">
    @endif

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{ elixir('dist/css/app.css') }}" rel="stylesheet">

    <meta name="alexaVerifyID" content="jcuPoQKjF9UOXxPKulzeux8w7g4"/>
    <meta name="msvalidate.01" content="48B0A933360DDEC6CF1775D7C7E28FD3"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta id="_token" name="_token" value="{{ csrf_token() }}"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @if (isset($canonical))
        <link rel="canonical" href="<?= url($canonical) ?>"/>
    @endif
</head>
<body id="app" class="{{ isset($bodyId) ? $bodyId : '' }}">
@include('partials.header')

<main>
    @yield('content')
    <footer class="footer hide">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-sm-10">
                    <ul class="bottom-menu-list">
                        <li><a href="/info/legal/terms-of-use">Terms of Use</a></li>
                        <li><a href="/info/legal/dmca">DMCA</a></li>
                        <li>
                            <a href="//www.iubenda.com/privacy-policy/863528"
                               class="iubenda-nostyle no-brand iubenda-embed" title="Privacy Policy">Privacy Policy</a>
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

    <footer class="page-footer">
        <div class="footer-copyright">
            <div class="container">
                © 2016 Videouri

                <a href="/info/legal/terms-of-use" class="grey-text right">
                    Terms of Use &nbsp; &nbsp;
                </a>
                <a href="/info/legal/dmca" class="grey-text right">
                    DMCA &nbsp; &nbsp;
                </a>
                <a href="//www.iubenda.com/privacy-policy/863528"
                   class="iubenda-nostyle no-brand iubenda-embed grey-text right" title="Privacy Policy">
                    Privacy Policy &nbsp; &nbsp;
                </a>
                <a href="javascript:void(0)" data-uv-lightbox="classic_widget" data-uv-mode="full"
                   data-uv-primary-color="#ffffff" data-uv-link-color="#444444" data-uv-default-mode="support"
                   data-uv-forum-id="151817" class="grey-text right">
                    Feedback &amp; Support &nbsp; &nbsp;
                </a>
            </div>
        </div>
    </footer>
</main>

<script type="text/javascript">
    // Iubenda
    (function (w, d) {
        var loader = function () {
            var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0];
            s.src = "//cdn.iubenda.com/iubenda.js";
            tag.parentNode.insertBefore(s, tag);
        };
        if (w.addEventListener) {
            w.addEventListener("load", loader, false);
        } else if (w.attachEvent) {
            w.attachEvent("onload", loader);
        } else {
            w.onload = loader;
        }
    })(window, document);

    var _iub = _iub || [];
    _iub.csConfiguration = {
        cookiePolicyId: 863528,
        siteId: 162117,
        lang: "en"
    };
    (function (w, d) {
        var loader = function () {
            var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0];
            s.src = "//cdn.iubenda.com/cookie_solution/iubenda_cs.js";
            tag.parentNode.insertBefore(s, tag);
        };
        if (w.addEventListener) {
            w.addEventListener("load", loader, false);
        } else if (w.attachEvent) {
            w.attachEvent("onload", loader);
        } else {
            w.onload = loader;
        }
    })(window, document);

    // Uservoice
    (function () {
        var uv = document.createElement('script');
        uv.type = 'text/javascript';
        uv.async = true;
        uv.src = '//widget.uservoice.com/RZAtJbQpNQqb9I2zEDpg.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(uv, s)
    })()
</script>

<!-- Scripts -->
@if (getenv('APP_ENV') == 'production')
    <script src="{{ elixir('dist/js/vendors.js') }}"></script>
    <script src="{{ elixir('dist/js/app.js') }}"></script>
@else
    <script src="/dist/js/vendors.js"></script>
    <script src="/dist/js/app.js"></script>
@endif

@yield('scripts')
</body>
</html>
