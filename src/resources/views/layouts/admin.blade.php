<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>BB Admin Framework</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>

{!! HTML::style("/css/admin.css?v=0.392") !!}
<!-- Latest compiled and minified CSS -->
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">--}}

{{--<!-- Optional theme -->--}}
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">--}}

<!-- Latest compiled and minified JavaScript -->
{!! HTML::style("/css/dashboard-css.css?v=0.2") !!}
{{--    {{ asset("css/admin-theme.css?v2.91") }}--}}
<!--BB:CSS-->
{{--{!! HTML::style(BBAdminThemeUrl(), array('id'=>'backend-css')) !!}--}}

{{--{!! BBlinkFonts() !!}--}}
{{--{!! BBFrameworkCss() !!}--}}
{{--{!! BBCustomCss() !!}--}}
{{--{!! HTML::style("resources/assets/css/builder-tool.css") !!}--}}
{!! HTML::script("/js/jquery-2.1.4.min.js") !!}
{!! HTML::script("/js/jquery-ui/jquery-ui.min.js") !!}
@yield('CSS')
@stack('css')

<!--BB:Theme-->

    <!--BB:JS-->
    @yield('HeaderJS')
</head>
<body data-background="[BB:Background]">
<div id="wrapper">
    @php
        $page = \Sahakavatar\Modules\Models\AdminPages::getPageByURL();
    @endphp
    @if($page)
        @if($page->left_bar)
            {!! BBleftBar() !!}
            <div id="main-wrapper" style="margin-left: 227px;">
                @else
                    <div id="main-wrapper" style="margin-left: 0px;">
                        @endif
                        @else
                            {!! BBleftBar() !!}
                            <div id="main-wrapper" style="margin-left: 227px;">
                                @endif
                                @if($page)
                                    @if($page->header)
                                        {!! BBheaderBack() !!}
                                    @endif
                                @else
                                    {!! BBheaderBack() !!}
                                @endif
                                <div class="middle-wrapper">
                                    <nav class="row">
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
                                                @php
                                                    Session::forget('message');
                                                    Session::forget('alert-class');
                                                @endphp
                                            </div>
                                        @endif
                                    </nav>
                                    <!--BB:PageContent-->
                                    @include(BBgetPageLayout(),['settings'=>BBgetPageLayoutSettings()])
                                    @yield('content')
                                    @yield('content2')


                                    {{--@yield('main_content')--}}
                                </div>
                            </div>
                    </div>
            </div>
</div>
@include('modal')
{{ csrf_field() }}

{!! HTML::script("/css/bootstrap/js/bootstrap.min.js") !!}
{!! HTML::script("/js/admin.js?v=6.0") !!}

{{--{!! HTML::script("resources/assets/js/bootbox/js/bootbox.min.js") !!}--}}
{{--{!! HTML::script("resources/assets/js/media-lightbox.js?v.5") !!}--}}
{{--{!! HTML::script("resources/assets/js/forms/multidata.js") !!}--}}

{{--{!! $javascript !!}--}}


<script>
    $(function () {
        if ($('[data-role="browseMedia"]').length > 0) {
            $('[data-role="browseMedia"]').media();
        }
    })
</script>
@yield('JS')
{!! BBscriptsHook() !!}
@stack('javascript')
@if(Session::has('message_code') && Session::pull('message_code') == 200)
    <script>
        $(function () {
            $('#message-modal .modal-body').html("{!! Session::pull('success_mes') !!}");
            $('#message-modal').modal();
        });
    </script>
@endif

</body>
{!! BBFrameworkJs() !!}
{{--{!! HTML::style("resources/assets/css/core_styles.css") !!}--}}

</html>

