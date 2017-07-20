@extends('layouts.admin')
@section('page_heading','Dashboard')
@section('content')
    <ol class="breadcrumb">
        <li><a href="/">Dashboard</a></li>
        <li class="active">Functions</li>
    </ol>
    <div class="content-page">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr class="bg-black-darker text-white">
                            <th width="15%">Function Name</th>
                            <th width="15%">Function</th>
                            <th width="200">Params</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($functions as $function)
                            <tr>
                                <td>{!! $function['name'] !!}</td>
                                <td>{!! $function['function'] !!}</td>
                                <td>{!! $function['params'] !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@stop
