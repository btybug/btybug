{!! HTML::style('custom/css/'.$page->slug.'.css') !!}
<body>
@php
    if(!isset($page)){
        $page = \Btybug\btybug\Services\RenderService::getFrontPageByURL();
    }
$settings=json_decode($page->settings,true);
if(isset($settings['file_path']) && \File::exists(module_path($settings['file_path']))){
include module_path($settings['file_path']);
}
@endphp


{{--{!! BBRenderUnits($page->template) !!}--}}



<body>