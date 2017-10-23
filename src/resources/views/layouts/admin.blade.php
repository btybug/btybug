<!DOCTYPE html>
@php
    $page = \Sahakavatar\Cms\Services\RenderService::getPageByURL();
@endphp

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
{{--{!! HTML::style("css/bootstrap/css/bootstrap.css") !!}--}}

{!! BBCss("backend")  !!}
{!! HTML::style("/css/admin.css?v=0.392") !!}
{!! HTML::style('custom/css/'.str_replace(' ','-',$page->title).'.css') !!}
@yield('CSS')
@stack('css')

<!--BB:Theme-->

    <!--BB:JS-->
    @yield('HeaderJS')
</head>
<body data-background="[BB:Background]">
<div id="wrapper">

    @if($page)
        {!! BBheaderBack() !!}
        <div style="background: #e2e2e2;">
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
            @include(BBgetPageLayout(),['settings'=>BBgetPageLayoutSettings()])
        </div>
    @endif
</div>
@include('modal')
{{ csrf_field() }}
{!! HTML::script("/js/jquery-2.1.4.min.js") !!}
{!! HTML::script("/js/jquery-ui/jquery-ui.min.js") !!}
<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
{!! BBMainJS() !!}
{!! HTML::script("/js/admin.js?v=6.0") !!}
{!! HTML::script('custom/js/'.str_replace(' ','-',$page->title).'.js') !!}
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
</html>

