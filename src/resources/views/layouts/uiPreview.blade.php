
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js" xmlns="http://www.w3.org/1999/html">
<!--<![endif]-->

<head>

    <meta charset="utf-8"/>
    <title>BB Admin Framework</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    {!! HTML::style('//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css') !!}

    {!! HTML::style('/js/jquery-ui/jquery-ui.min.css') !!}

    {!! HTML::script("/css/bootstrap/js/bootstrap.min.js") !!}
    {!! HTML::style('/css/cms.css') !!}
    {!! HTML::script("/js/jquery-2.1.4.min.js") !!}
    {!! HTML::script("/js/jquery-ui/jquery-ui.min.js") !!}
    {!! HTML::script("/js/tinymice/tinymce.min.js") !!}
    {!! HTML::style("css/animate.css") !!}
    {{--{!! HTML::style("/css/core_styles.css") !!}--}}
   
    {{--{!! BBlinkFonts() !!}--}}
    @yield('CSS')
    @stack('css')
</head>
<body>


<div class="container-fluid coreheadersetting m-b-10">
    <div class="row">
          <div class="col-xs-4 p-t-10">
                  <div class="form-group">
                    <label class="sr-only" for="itemname">itemname</label>
                    <input type="text" class="form-control" id="itemname" placeholder="itemname" value="{!! $variation->title or null !!}">
                  </div>
          </div>
          <div class="col-xs-4  p-t-10">
                  <button class="btn btn-success" data-settingaction="save"> save</button>
          </div>
          <div class="col-xs-4 text-right  p-t-10">
                  <button class="btn btn-info" data-settingaction="setting"> Setting</button>
          </div>
    </div>
</div>
<header class="hide">
    @include('cms::header')
</header>

<div class="modal fade" id="magic-settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    {{--{!! Form::open(['url'=>'/admin/backend/theme-edit/live-save', 'id'=>'magic-form']) !!}--}}
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{--{!! Form::submit('Save',['class' => 'btn btn-success pull-right m-r-10']) !!}--}}
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" style="min-height: 500px;">

                <div id="magic-body">

                </div>
            </div>
        </div>
    </div>
    {{--{!! Form::close() !!}--}}
</div>


<div class="previewlivesetting" >
      <div data-loadifram="preview">
        @yield('content')
      </div>
      <div>
           @yield('settings')
      </div>
</div>

<input name="token" type="hidden" value="{{ csrf_token() }}" id="token"/>

</body>
{{--{!! HTML::script("/js/UiElements/bb_styles.js?v.5") !!}--}}
{{--{!! HTML::script("/js/UiElements/ui-preview-setting.js") !!}--}}

@yield('JS')
@stack('javascript')
</html>