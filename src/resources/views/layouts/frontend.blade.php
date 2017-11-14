<!DOCTYPE html>

@php
    if(!isset($page)){
        $page = \Btybug\btybug\Services\RenderService::getFrontPageByURL();
    }

@endphp
<html lang="@yield('locale')">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @yield('metas')
    <link type="image/x-icon" rel="icon" href="{{ asset('assets/favicon.ico') }}"/>
    <link type="image/x-icon" rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}"/>
    {!! BBCss()  !!}
    <link rel="stylesheet" href="{{ url("public/css/font-awesome/css/font-awesome.min.css") }}"/>
    <link rel="stylesheet" href="{{ url("public/js/jquery-ui/jquery-ui.min.css") }}"/>
    <link rel="apple-touch-icon" href="{{ asset('assets/apple-touch-icon.png') }}"/>
    {!! HTML::style('public-x/custom/css/'.str_replace(' ','-',$page->title).'.css') !!}
    {{--<link rel="stylesheet" href="{{ asset("resources/assets/css/bootstrap.css?v=1.1") }}" />--}}
    @yield('css')
    @stack('CSS')
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    {!! HTML::script("public/js/jquery-2.1.4.min.js") !!}
    <script src="{{ url("public/js/jquery-ui/jquery-ui.min.js") }}" type="text/javascript"></script>
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    {!! HTML::script('public-x/custom/js/'.str_replace(' ','-',$page->title).'.js') !!}
    {!! HTML::script("public/js/tinymice/tinymce.min.js") !!}
    {!! HTML::script("public/js/UiElements/bb_iframejs.js") !!}
</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-custom">
@if (isset($errors) && count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{!! $error !!}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('flash.message') != null)
    <div class="flash alert {{ Session::has('flash.class') ? session('flash.class') : 'alert-success' }}"
         role="alert">
        <button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        {!! session('flash.message') !!}
    </div>
@endif

@if(Session::has('message'))
    <div class="m-t-10 alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible"
         role="alert">
        <button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        {!! Session::get('message') !!}
    </div>
@endif
@yield('content')
<!-- jQuery first, then Bootstrap JS. -->
{!! BBJquery() !!}
{!! BBMainFrontJS() !!}
{!! BBJs() !!}
@yield('js')
</body>
</html>