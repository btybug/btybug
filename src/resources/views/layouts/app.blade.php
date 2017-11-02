<!DOCTYPE html>
@php
    if(!isset($page)){
        $page = \Btybug\btybug\Services\RenderService::getFrontPageByURL();
    }

@endphp
<html>
<head>
    {!! HTML::style('custom/css/'.$page->slug.'.css') !!}

</head>
<body>
@yield('content')
{!! HTML::script('custom/js/'.str_replace(' ','-',$page->slug).'.js') !!}

</body>
</html>
