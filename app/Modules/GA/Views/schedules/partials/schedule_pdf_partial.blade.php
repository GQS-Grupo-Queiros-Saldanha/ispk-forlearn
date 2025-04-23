
@extends('layouts.printForSchedule')
<title>Horário_{{$classe->code}}_{{ $schedule->schedule_type->currentTranslation->display_name }} </title>
@section('content')
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'HORÁRIO';
        $discipline_code = '';
    @endphp
    <main>
        @include('Reports::pdf_model.forLEARN_header')
        
        @include('GA::schedules.schedule_pdf')
        
        @include('Reports::pdf_model.signature')

    </main>
    
    @endsection
    