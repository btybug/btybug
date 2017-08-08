<!DOCTYPE html>
<html lang="@yield('locale')">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @yield('metas')
    <link type="image/x-icon" rel="icon" href="{{ asset('assets/favicon.ico') }}" />
    <link type="image/x-icon" rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ url("css/bootstrap/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ url("css/font-awesome/css/font-awesome.min.css") }}" />
    <link rel="stylesheet" href="{{ url("js/jquery-ui/jquery-ui.min.css") }}" />
    {{--{!! HTML::style("/css/admin.css?v=0.392") !!}--}}
    {{--{!! HTML::style("/css/dashboard-css.css?v=0.2") !!}--}}
    {{--{!! HTML::style("/css/admin-theme.css?v2.91",['id' => 'stylecolor']) !!}--}}

<!--BB:CSS-->
    {{--{!! HTML::style("/css/core_styles.css") !!}--}}
    {{--{!! HTML::style("/resources/views/layouts/themes/admintheme/css/style.css") !!}--}}
    {{--{!! HTML::style("/resources/views/layouts/themes/admintheme/css/font-awesome/css/font-awesome.min.css") !!}--}}
    {{--{!! BBlinkFonts() !!}--}}
    {!! \Sahakavatar\Framework\Models\Framework::activeCss() !!}
    {!! \Sahakavatar\Framework\Models\Framework::customCss() !!}
    <link rel="apple-touch-icon" href="{{ asset('assets/apple-touch-icon.png') }}" />
    {{--<link rel="stylesheet" href="{{ asset("resources/assets/css/bootstrap.css?v=1.1") }}" />--}}
    @yield('css')
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="{{ url("js/jquery-3.2.1.min.js") }}" type="text/javascript"></script>
    <script src="{{ url("js/jquery-ui/jquery-ui.min.js") }}" type="text/javascript"></script>
    <script src="{{ url("css/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

    {!! HTML::script("/js/tinymice/tinymce.min.js") !!}
    {!! HTML::script("/js/UiElements/bb_iframejs.js") !!}
</head>
<body>
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

@yield('js')
</body>
</html>