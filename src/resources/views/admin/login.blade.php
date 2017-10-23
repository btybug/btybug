<style> .m-t-100 {
        margin-top: 100px;
    }</style>
@extends('layouts.login')
@section('content')
    <div class="row login m-t-100">
        <div class="col-md-6 col-md-offset-3">
            @if (session('flash.message') != null)
                <div class="flash alert {{ Session::has('flash.class') ? session('flash.class') : 'alert-success' }}">
                    {!! session('flash.message') !!}
                </div>
            @endif

            {{--[getCoreForm slug=registration]--}}
            {!! Form::open() !!}
            <fieldset>

                <div class="form-group">
                    <input class="form-control" placeholder="Username or Email" name="usernameOremail">
                </div>
                <div class="form-group">
                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                </div>
                <div class="checkbox">
                    <label>
                        <input name="remember" type="checkbox" value="Remember Me"> Remember Me
                    </label>
                </div>
                <input class="btn btn-lg btn-success btn-block" type="submit" value="Login">
                <p class="text-center">
                    <a href="{!! url('forgot')!!}" class="forgot-pass">Forget Password</a>
                </p>

                {{--{!! BBRenderWidget('57b1b22836979.57b1b22876ebb') !!}--}}


            </fieldset>
            {!! Form::close() !!}
        </div>
    </div>
    <script>
        window.fbAsyncInit = function () {
            FB.init({
                appId: '984676318257432',
                xfbml: true,
                version: 'v2.5'
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
@stop
