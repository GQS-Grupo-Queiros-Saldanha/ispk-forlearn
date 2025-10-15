<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Tipo de Metríca')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tipo_metrica.index') }}">Tipo de Metricas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page"> {{ $tipoMetrica->nome }} </li>
@endsection
@section('body')
    <div class="row">
        <div class="col-6 p-2">
            <label>Código</label>
            <input type="text" value="{{ $tipoMetrica->codigo }}" disabled class="form-control">
        </div>
        <div class="col-6 p-2">
            <label>Nome</label>
            <input type="text" value="{{ $tipoMetrica->nome }}" disabled class="form-control">
        </div>
        <div class="col-6 p-2">
            <label>Descrição</label>
            <input type="text" value="{{ $tipoMetrica->descricao }}" disabled class="form-control">
        </div>
        <div class="col-6 p-2">
            <label>Abreviatura</label>
            <input type="text" value="{{ $tipoMetrica->abreviatura }}" disabled class="form-control">
        </div>
    </div>
@endsection
