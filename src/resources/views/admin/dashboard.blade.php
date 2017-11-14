@extends('btybug::layouts.admin')
@section('content')
    <div class="d1">
        <h1>Tab</h1>
        <p>div-class=top-nav-btybug</p>
        <div class="top-nav-btybug">
            <ul>
                <li class="active"><a href="#">Tab 1</a></li>
                <li><a href="#">Tab 2</a></li>
                <li><a href="#">Tab 3</a></li>
            </ul>
        </div>
    </div>
    <div class="d2">
        <h1>Table</h1>
        <p>table-class=table table-btybug</p>
        <p>td-span-class-active=td-active</p>
        <p>td-span-class-inactive=td-inactive</p>
        <table class="table table-btybug">
            <thead>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Lorem 1</td>
                <td>Lorem 2</td>
                <td><span class="td-inactive">Lorem 3</span></td>
                <td>Lorem 4</td>
                <td>Lorem 5</td>
                <td>Lorem 6</td>
            </tr>
            <tr>
                <td>Lorem 7</td>
                <td>Lorem 8</td>
                <td><span class="td-active">Lorem 9</span></td>
                <td>Lorem 10</td>
                <td>Lorem 11</td>
                <td>Lorem 12</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d3">
        <h1>Buttons</h1>
        <h3>Acction</h3>
        <button class="action-button-blue">Blue</button>
        <button class="action-button-red">Red</button>
        <h3>Add</h3>
        <button class="button-add-btybug bt-lg"><span>Add</span></button>
        <button class="button-add-btybug"><span>Add</span></button>
        <button class="button-add-btybug bt-sm"><span>Add</span></button>
        <h3>Upload</h3>
        <button class="button-upload-btybug bt-lg"><span>Upload</span></button>
        <button class="button-upload-btybug"><span>Upload</span></button>
        <button class="button-upload-btybug bt-sm"><span>Upload</span></button>
        <h3>Save</h3>
        <button class="button-save-btybug bt-lg"><span>Save</span></button>
        <button class="button-save-btybug"><span>Save</span></button>
        <button class="button-save-btybug bt-sm"><span>Save</span></button>
        <h3>View</h3>
        <button class="button-view-btybug bt-lg"><span>View</span></button>
        <button class="button-view-btybug"><span>View</span></button>
        <button class="button-view-btybug bt-sm"><span>View</span></button>
        <h3>Edit,Delete,View</h3>
        <a class="button-btybug bt-edit" href="#"></a>
        <a class="button-btybug bt-delete" href="#"></a>
        <a class="button-btybug bt-view" href="#"></a>
    </div>
    <div class="d4">
        <h1>Panel</h1>
        <h3>Panel Default</h3>
        <div class="panel-btybug">
            <div class="heading">
                <span class="icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                <span class="title">Panel Title</span>
            </div>
            <div class="content">
                <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>
            </div>
        </div>
        <h3>Panel Collapse</h3>
                <div class="panel-btybug">
                    <div class="heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <span class="icon"><i class="fa fa-chevron-down" aria-hidden="true"></i></span>
                                <span class="title">Panel Title</span>
                            </a>
                    </div>
                    <div id="collapseOne" class="collapse in">
                        <div class="content">
                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>
                        </div>
                    </div>
                </div>



    </div>
@stop
@section('CSS')
    {{--{!! HTML::style('public/css/dashboard-css.css?v=0.1') !!}--}}
    {!! BBstyle(modules_path('cms/src/resources/assets/dashboard-css.css')) !!}
@stop

@section('JS')
    {!! HTML::script('public/js/dashboard.js?v=0.9') !!}
@stop

