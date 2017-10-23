@extends('layouts.admin')
@section('page_heading','Dashboard')
@section('content')
    <ol class="breadcrumb">
        <li><a href="/">Dashboard</a></li>
        <li class="active">Notes</li>
    </ol>
    <div class="content-page">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div id="notes-app" class="widget">
                        <div class="notes-line"></div>
                        <div class="widget-header centered transparent">
                            <div class="left-btn btn-group">
                                <a class="btn btn-sm btn-primary add-note"><i class="fa fa-plus"></i></a>
                                <a class="btn btn-sm btn-primary back-note-list"><i class="fa fa-bars"></i></a>
                            </div>
                            <h2>Notes</h2>
                            <div class="additional-btn">
                                <a href="#" class="hidden reload"><i class="fa fa-undo icon-ccw-1"></i></a>
                                <a href="#" class="widget-popout hidden tt" title="Pop Out/In"><i
                                            class="fa fa-caret-square-o-up icon-publish"></i></a>
                                <a href="#" class="widget-maximize hidden"><i
                                            class="fa fa-arrows-alt icon-resize-full-1"></i></a>
                                <a href="#" class="widget-toggle"><i
                                            class="fa fa-chevron-down icon-down-open-2"></i></a>
                                <a href="#" class="widget-close"><i class="fa fa-times icon-cancel-3"></i></a>
                            </div>
                        </div>
                        <div class="widget-content padding-sm">
                            <div id="notes-list">
                                <div class="scroller">
                                    <ul class="list-unstyled">
                                    </ul>
                                </div>
                            </div>
                            <div id="note-data">
                                <form>
                                    <textarea class="form-control" id="note-text" placeholder="Your note..."></textarea>
                                </form>
                            </div>
                            <div class="status-indicator">Saved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
@section('CSS')
    <link rel="stylesheet" href="{{ asset("public/css/notes/style.css") }}"/>
    <link rel="stylesheet" href="{{ asset("public/css/notes/prettify.css") }}"/>
    <link rel="stylesheet" href="{{ asset("public/css/notes/bootstrap-select.min.css") }}"/>
@stop
@section('JS')
    <script>
        var resizefunc = [];
    </script>
    {!! HTML::script('/public/js/notes/init.js') !!}
    {!! HTML::script('/public/js/notes/fastclick.js') !!}
    {!! HTML::script('/public/js/notes/jquery-sparkline.js') !!}
    {!! HTML::script('/public/js/notes/sparkline-charts.js') !!}
    {!! HTML::script('/public/js/notes/prettify.js') !!}
    {!! HTML::script('/public/js/notes/bootstrap-select.min.js') !!}
    {!! HTML::script('/public/js/notes/jquery.slimscroll.min.js') !!}
    {!! HTML::script('/public/js/notes/bootstrap.file-input.js') !!}
    {!! HTML::script('/public/js/notes/jquery.browser.min.js') !!}
    {!! HTML::script('/public/js/notes/bootbox.min.js') !!}
    {!! HTML::script('/public/js/notes/jquery.blockUI.js') !!}
    {!! HTML::script('/public/js/notes/notes.js') !!}

    <script>
        $(function () {

        })
    </script>
@stop
