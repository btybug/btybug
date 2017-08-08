@extends('cms::layouts.admin')

    @section('content')
    @stop
@section('CSS')
       {!! HTML::style('/css/dashboard-css.css?v=0.1') !!}
     @stop

     @section('JS')
       {!! HTML::script('/js/dashboard.js?v=0.9') !!}
     @stop

