<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Relatório Primário dos Graduados')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Relatório Primário dos Graduados</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/new_switcher.css') }}">
@endsection
@section('selects')

@endsection
@section('body')
    /** Generate Report Form **/
    {!! Form::open(['route' => ['report.primary.xls']]) !!}
    <div class="mb-2 col-md-3">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="card mb-2" style="float: right;margin-right:2px;">
        <button type="submit" class="btn btn-success" >
            <i class="fas fa-file-pdf" id="icone_publish"></i>
            Gerar Relatório
        </button>
    </div>
    {!! Form::close() !!}
@endsection
