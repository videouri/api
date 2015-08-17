<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <meta name="description" content="" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <meta property="og:title" content="">
    <meta property="og:site_name" content="Videouri"/>
    <meta property="og:url" content="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>"/>
    <meta property="og:description" content="" />

    <?php if(isset($img)): ?>
    <meta property="og:type" content="video"/>
    <meta property="og:image" content="<?= $img ?>">
    <?php endif ?>

    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

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
        
    <?php if(isset($canonical)) : ?>
    <link rel="canonical" href="<?= url($canonical) ?>" />
    <?php endif ?>

    <?php if (false): ?>
        <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-28752800-1', 'auto');
            ga('require', 'displayfeatures');
            ga('send', 'pageview');
        </script>
    <?php endif; ?>
</head>
<body>
    @include('partials/nav')
    
    <div class="container" id="content">
    @yield('content')
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-sm-10">
                    <ul class="bottom-menu-list">
                        <li><a href="/legal/termsofuse">Terms of Use</a></li>
                        <li><a href="/legal/dmca">DMCA</a></li>
                        <li>
                            <a href="//www.iubenda.com/privacy-policy/863528" class="iubenda-nostyle no-brand iubenda-embed" title="Privacy Policy">Privacy Policy</a>
                        </li>
                        <li class="hidden">
                            <a href="" class="family-filter">
                                {{-- @todo --}}
                                Turn on / Turn off - family filter 
                            </a>
                        </li>
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

    <?php if (false): // @TODO ?>
    <!-- .modal -->
    <div id="videouri-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <p><i class="icon-spinner icon-spin icon-large"></i> Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn pull-left" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div>
    <!-- / .modal -->
    <?php endif ?>

    <script type="text/javascript">
        (function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);
    </script>

    <!-- Scripts -->
    <script src="{{ asset('/js/app.js') }}"></script>
</body>
</html>
