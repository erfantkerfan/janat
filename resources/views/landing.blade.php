<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/landing-page.css" rel="stylesheet"/>
    <link href="{{ mix('css/font.css') }}" rel="stylesheet">

    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

</head>
<body class="landing-page landing-page1">
<nav class="navbar navbar-transparent navbar-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button id="menu-toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar bar1"></span>
                <span class="icon-bar bar2"></span>
                <span class="icon-bar bar3"></span>
            </button>
            <a href="{{ url('/') }}">
                <div class="logo-container">
                    <div class="logo">
                        <img src="/img/logo.png" alt="صندوق قرض الحسه جنت">
                    </div>
                    <div class="brand">
                        قرض الحسه جنت
                    </div>
                </div>
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="example" >
            <ul class="nav navbar-nav navbar-right">
                @auth
                    <li>
                        <a href="{{ url('/dashboard') }}">{{ __('navbar.Dashboard') }}</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('navbar.Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <input type="submit" value="logout" style="display: none">
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}">{{ __('navbar.Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li>
                            <a href="{{ route('register') }}">{{ __('navbar.Register') }}</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
</nav>
<div class="wrapper">
    <div class="parallax filter-gradient blue" data-color="blue">
        <div class="parallax-background">
            <img class="parallax-background-image" src="/assets/img/landing-page/xns6snpdxy5f.jpeg">
        </div>
        <div class= "container">
            <div class="row">
                <div class="col-md-5 hidden-xs">
                    <div class="parallax-image">
                        <img class="phone" src="/assets/img/landing-page/121641871902064719057100271551181214170178.png" style="margin-top: 20px"/>
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-1">
                    <div class="description">
                        <h2>صندوق قرض الحسنه جنت</h2>
                        <br>
                        <h5 style="text-align: justify;">
                            در این اوضاع‌ و احوال کدام بانک یا صندوق را سراغ دارید که با کارمزدها و سودهای کلان دود از سرتان بلند نکرده و کارتان را زود راه بیاندازد؟!!
                        </h5>
                    </div>
                    @if(false)
                    <div class="buttons">
                        <button class="btn btn-fill btn-neutral">
                            <i class="fa fa-apple"></i> Appstore
                        </button>
                        <button class="btn btn-simple btn-neutral">
                            <i class="fa fa-android"></i>
                        </button>
                        <button class="btn btn-simple btn-neutral">
                            <i class="fa fa-windows"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="section section-presentation">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="description">
                        <h4 class="header-text">وامی که هیچ سود و کارمزدی ندارد</h4>
                        <p style="text-align: justify;">در این زمانه اعتماد اولین حرف را در رابطه انسان ها می زند. کاملا مشخص است که شما به اعضای خانواده خود راحتتر از همکارانتان قرض می دهید، چون به آن ها اعتماد دارید. همینطور که همه ما می دانیم، بانک به همین سادگی وام را متعلق به درخواست کننده نمی داند. که کاملا منطقی است چون بانک به شما اعتماد ندارد و شناختی به شما نیز ندارد. چک، سابقه بیمه، ضامن، فیش حقوقی، فتوکپی از اتمام اسناد هویتی و غیره همه از مواردی است که بانک از شما درخواست می کند تا به شما اعتماد پیدا کند. در صورتی که در صندوق های قرض الحسنه نیازی به این موارد نیست.</p>
                    </div>
                </div>
                <div class="col-md-5 col-md-offset-1 hidden-xs">
                    <img src="/assets/img/landing-page/Family-bank-account-1.jpg"/>
                </div>
            </div>
        </div>
    </div>
    @if(false)
    <div class="section section-demo">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div id="description-carousel" class="carousel fade" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="item">
                                <img src="assets/img/template/examples/home_33.jpg" alt="">
                            </div>
                            <div class="item active">
                                <img src="assets/img/template/examples/home_22.jpg" alt="">
                            </div>
                            <div class="item">
                                <img src="assets/img/template/examples/home_11.jpg" alt="">
                            </div>
                        </div>
                        <ol class="carousel-indicators carousel-indicators-blue">
                            <li data-target="#description-carousel" data-slide-to="0" class=""></li>
                            <li data-target="#description-carousel" data-slide-to="1" class="active"></li>
                            <li data-target="#description-carousel" data-slide-to="2" class=""></li>
                        </ol>
                    </div>
                </div>
                <div class="col-md-5 col-md-offset-1">
                    <h4 class="header-text">Easy to integrate</h4>
                    <p>
                        With all the apps that users love! Make it easy for users to share, like, post and tweet their favourite things from the app. Be sure to let users know they continue to remain connected while using your app!
                    </p>
                    <a href="http://www.creative-tim.com/product/awesome-landing-page" id="Demo3" class="btn btn-fill btn-info" data-button="info">Get Free Demo</a>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="section section-gray section-clients">
        <div class="container text-center">
            @if(false)
                <h4 class="header-text">Friends in high places</h4>
            @endif
            <p>
                این روزها دست، جیب و حساب خیلی‌ها خالی بوده و شرایط سخت اقتصادی عده‌ای را بیشتر تحت ‌فشار قرار داده است. در چنین شرایطی گاهی یک وام خوب با شرایط مناسب که با دخل ‌و خرج آدم‌ها جور دربیاید، می‌تواند گوشه‌ای از کار را گرفته و دوایی بر دردها باشد. اما در این اوضاع‌ و احوال کدام بانک یا صندوق را سراغ دارید که با کارمزدها و سودهای کلان دود ازسرتان بلند نکرده و کارتان را زود راه بیاندازد؟!!
            </p>
            @if(false)
                <div class="logos">
                    <ul class="list-unstyled">
                        <li ><img src="assets/img/logos/adobe.png"/></li>
                        <li ><img src="assets/img/logos/zendesk.png"/></li>
                        <li ><img src="assets/img/logos/ebay.png"/></i>
                        <li ><img src="assets/img/logos/evernote.png"/></li>
                        <li ><img src="assets/img/logos/airbnb.png"/></li>
                        <li ><img src="assets/img/logos/zappos.png"/></li>
                    </ul>
                </div>
            @endif
{{--                <img class="phone" src="/assets/img/landing-page/flat-icon-cashbox1.png" style="margin-top: 20px"/>--}}
        </div>
    </div>
    <div class="section section-features">
        <div class="container">
            <h4 class="header-text text-center">ویژگی ها</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-blue">
                        <div class="icon">
                            <i class="pe-7s-note2"></i>
                        </div>
                        <div class="text">
                            <h4>مدیریت آنلاین کاربران</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-blue">
                        <div class="icon">
                            <i class="pe-7s-bell"></i>
                        </div>
                        <h4>اعلان های هوشمند</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-blue">
                        <div class="icon">
                            <i class="pe-7s-graph1"></i>
                        </div>
                        <h4>نظارت بر تمام اطلاعات صندوق</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(false)
    <div class="section section-testimonial">
        <div class="container">
            <h4 class="header-text text-center">What people think</h4>
            <div id="carousel-example-generic" class="carousel fade" data-ride="carousel">
                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item">
                        <div class="mask">
                            <img src="assets/img/faces/face-4.jpg">
                        </div>
                        <div class="carousel-testimonial-caption">
                            <p>Jay Z, Producer</p>
                            <h3>"I absolutely love your app! It's truly amazing and looks awesome!"</h3>
                        </div>
                    </div>
                    <div class="item active">
                        <div class="mask">
                            <img src="assets/img/faces/face-3.jpg">
                        </div>
                        <div class="carousel-testimonial-caption">
                            <p>Drake, Artist</p>
                            <h3>"This is one of the most awesome apps I've ever seen! Wish you luck Creative Tim!"</h3>
                        </div>
                    </div>
                    <div class="item">
                        <div class="mask">
                            <img src="assets/img/faces/face-2.jpg">
                        </div>
                        <div class="carousel-testimonial-caption">
                            <p>Rick Ross, Musician</p>
                            <h3>"Loving this! Just picked it up the other day. Thank you for the work you put into this."</h3>
                        </div>
                    </div>
                </div>
                <ol class="carousel-indicators carousel-indicators-blue">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>
            </div>
        </div>
    </div>
    @endif
    <div class="section section-no-padding">
        <div class="parallax filter-gradient blue" data-color="blue">
            <div class="parallax-background">
                <img class ="parallax-background-image" src="assets/img/template/bg3.jpg"/>
            </div>
            <div class="info">
                @if(false)
                    <h1>Download this landing page for free!</h1>
                @endif
                <p>همین صندوق‌های کوچک قرض‌الحسنه که مبلغ آن در ابتدا به چشم هم نمی‌آید، می‌تواند گره از برخی مشکلات مالی یک خانواده باز کند.</p>
                @if(false)
                    <a href="http://www.creative-tim.com/product/awesome-landing-page" class="btn btn-neutral btn-lg btn-fill">DOWNLOAD</a>
                @endif
            </div>
        </div>
    </div>
    @if(false)
    <footer class="footer">
        <div class="container">
            <nav class="pull-left">
                <ul>
                    <li>
                        <a href="#">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Company
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Portfolio
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Blog
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="social-area pull-right">
                <a class="btn btn-social btn-facebook btn-simple">
                    <i class="fa fa-facebook-square"></i>
                </a>
                <a class="btn btn-social btn-twitter btn-simple">
                    <i class="fa fa-twitter"></i>
                </a>
                <a class="btn btn-social btn-pinterest btn-simple">
                    <i class="fa fa-pinterest"></i>
                </a>
            </div>
            <div class="copyright">
                &copy; 2016 <a href="http://www.creative-tim.com">Creative Tim</a>, made with love
            </div>
        </div>
    </footer>
    @endif
</div>

</body>
<script src="assets/js/jquery-1.10.2.js" type="text/javascript"></script>
<script src="assets/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.js" type="text/javascript"></script>
<script src="assets/js/awesome-landing-page.js" type="text/javascript"></script>
</html>
