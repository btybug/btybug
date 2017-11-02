@extends('btybug::layouts.frontend')
@section('content')
    @if(isset($settings['pl']) && isset($settings['pl_live_settings']))
        <textarea data-pagejson="setting" class="hide">{!! json_encode($settings,true) !!}</textarea>
        @if(isset($settings['header']) && $settings['header'])
            {!! BBheader() !!}
        @endif
        @if(isset($settings['mw']) && $settings['mw'])
            {!! BBRenderPageSections($settings['pl'],$settings,$settings['mw']) !!}
        @else
            {!! BBRenderPageSections($settings['pl'],$settings,$page->main_view) !!}
        @endif
        @if(isset($settings['footer']) && $settings['footer'])
            {!! BBfooter() !!}
        @endif
    @else
        @if($page->header)
            {!! BBheader() !!}
        @endif
        {!! BBRenderPageSections($page->page_layout,$settings) !!}
        @if($page->footer)
            {!! BBfooter() !!}
        @endif
    @endif
@stop