<!--

=========================================================
* Vue Material Dashboard PRO - v1.4.0
=========================================================

* Product Page: https://www.creative-tim.com/product/vue-material-dashboard-pro
* Copyright 2019 Creative Tim (https://www.creative-tim.com)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

-->

<!DOCTYPE html>
<html lang="en" dir="rtl" translate="no">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=1">

    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>

    <!-- Canonical SEO -->
    <!--  <link rel=canonical href=https://www.creative-tim.com/product/vue-material-dashboard-laravel>-->

{{--  <!--  Social tags      -->--}}
{{--  <meta name=keywords content="creative tim, updivision, html dashboard, vue, vuejs, laravel, json:api, json, api, html css dashboard laravel, vue material dashboard laravel, vue material dashboard, material vue admin, vue dashboard, vue admin, web dashboard, bootstrap 4 dashboard laravel, bootstrap 4, css3 dashboard, bootstrap 4 admin laravel, material ui dashboard bootstrap 4 laravel, frontend, responsive bootstrap 4 dashboard, material design, material laravel bootstrap 4 dashboard">--}}
{{--  <meta name=description content="Vue Material Dashboard Laravel helps you build awesome apps with a flexible architecture. It comes with UI components, a Vue Material frontend and an API-powered Laravel backend.">--}}

{{--  <!-- Schema.org markup for Google+ -->--}}
{{--  <meta itemprop=name content="Vue Material Dashboard Laravel by Creative Tim & UPDIVISION">--}}
{{--  <meta itemprop=description content="Vue Material Dashboard Laravel helps you build awesome apps with a flexible architecture. It comes with UI components, a Vue Material frontend and an API-powered Laravel backend.">--}}
{{--  <meta itemprop=image content=https://s3.amazonaws.com/creativetim_bucket/products/331/original/opt_md_vuelaravel_thumbnail.jpg>--}}

{{--  <!-- Twitter Card data -->--}}
{{--  <meta name=twitter:card content=product>--}}
{{--  <meta name=twitter:site content=@creativetim>--}}
{{--  <meta name=twitter:title content="Vue Material Dashboard Laravel by Creative Tim & UPDIVISION">--}}
{{--  <meta name=twitter:description content="Vue Material Dashboard Laravel helps you build awesome apps with a flexible architecture. It comes with UI components, a Vue Material frontend and an API-powered Laravel backend.">--}}
{{--  <meta name=twitter:creator content=@creativetim>--}}
{{--  <meta name=twitter:image content=https://s3.amazonaws.com/creativetim_bucket/products/331/original/opt_md_vuelaravel_thumbnail.jpg>--}}
{{--  <meta name=twitter:data1 content="Vue Material Dashboard Laravel by Creative Tim & UPDIVISION">--}}
{{--  <meta name=twitter:label1 content="Product Type">--}}
{{--  <meta name=twitter:label2 content=Price>--}}

{{--  <!-- Open Graph data -->--}}
{{--  <meta property=fb:app_id content=655968634437471>--}}
{{--  <meta property=og:title content="Vue Material Dashboard Laravel by Creative Tim & UPDIVISION">--}}
{{--  <meta property=og:type content=article>--}}
{{--  <meta property=og:url content=https://demos.creative-tim.com/vue-material-dashboard-laravel>--}}
{{--  <meta property=og:image content=https://s3.amazonaws.com/creativetim_bucket/products/331/original/opt_md_vuelaravel_thumbnail.jpg>--}}
{{--  <meta property=og:description content="Vue Material Dashboard Laravel helps you build awesome apps with a flexible architecture. It comes with UI components, a Vue Material frontend and an API-powered Laravel backend.">--}}
{{--  <meta property=og:site_name content="Creative Tim">--}}

<!--     Fonts and icons     -->
{{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,300italic,400italic">--}}
{{--    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">--}}

{{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" rel="stylesheet">--}}

    {{--  <!-- Google Tag Manager -->--}}
    {{--<!--  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':-->--}}
    {{--<!--      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],-->--}}
    {{--<!--    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=-->--}}
    {{--<!--    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);-->--}}
    {{--<!--  })(window,document,'script','dataLayer','GTM-NKDMSK6');</script>-->--}}
    {{--  <!-- End Google Tag Manager -->--}}
</head>

<body class="sidebar-image rtl">

{{--<!-- Google Tag Manager (noscript) -->--}}
{{--<!--<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKDMSK6" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>-->--}}
{{--<!-- End Google Tag Manager (noscript) -->--}}

<div id="dashboard_app">
    <app :user="{{ json_encode($user) }}"
         :settings="{{ json_encode($settings) }}"
    ></app>
</div>
<!-- built files will be auto injected -->

{{--<!--<script>-->--}}

{{--<!--  // Google Analytics-->--}}
{{--<!--  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){-->--}}
{{--<!--    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),-->--}}
{{--<!--    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)-->--}}
{{--<!--  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');-->--}}
{{--<!--  ga('create', 'UA-46172202-22', 'auto', {allowLinker: true});-->--}}
{{--<!--  ga('set', 'anonymizeIp', true);-->--}}
{{--<!--  ga('require', 'GTM-K9BGS8K');-->--}}
{{--<!--  ga('require', 'displayfeatures');-->--}}
{{--<!--  ga('require', 'linker');-->--}}
{{--<!--  ga('linker:autoLink', ["2checkout.com","avangate.com"]);-->--}}

{{--<!--</script>-->--}}
{{--<!--<noscript>-->--}}
{{--<!--  <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=111649226022273&ev=PageView&noscript=1"/>-->--}}
{{--<!--</noscript>-->--}}

{{--<!-- Scripts -->--}}
<script src="{{ mix('js/app.js') }}" defer></script>

<style>
    .md-menu-content.md-select-menu {
        z-index: 9999;
    }
</style>

</body>
</html>
