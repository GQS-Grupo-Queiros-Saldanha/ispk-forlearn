@php

$data_title = "N_devedores_".date("Y-m-d");
@endphp

@section('title', "".$data_title)
@extends('layouts.printForSchedule')
@section('content')

        <style>
        .div-top{
            padding-top: 10px!important;
            padding-bottom: 10px!important;
            background-size: 90px!important;
        }
    </style>
    
    <main>
        @include('Reports::pdf_model.pdf_header')
    

        @include('Reports::partials.total-articles')



    </main>

@endsection

<script>

</script>

