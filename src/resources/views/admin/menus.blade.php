@extends('layouts.admin')

@section('content')
    Admin menus
@section('CSS')
    {!! HTML::style('/public/css/dashboard-css.css?v=0.1') !!}
@stop

@section('JS')
    {!! HTML::script('public/js/dashboard.js?v=0.9') !!}
@stop

@stop