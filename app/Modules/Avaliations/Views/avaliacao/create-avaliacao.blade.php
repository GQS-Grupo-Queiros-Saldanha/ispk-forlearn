<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Criar avaliação')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('avaliacao.index') }}">Avaliação</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
    {!! Form::open(['route' => ['avaliacao.store']]) !!}

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                ×
            </button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-6 p-2">
            <label>Nome</label>
            <input type="text" name="nome" id="" class="form-control" required>
        </div>
        <div class="col-6 p-2">
            <label>Tipo de Avaliação</label>
            <select name="tipo_avaliacao" id="" class="form-control" required>
                <option value=""></option>
                @foreach ($tipo_avaliacaos as $tipo_avaliacao)
                    <option value="{{ $tipo_avaliacao->id }}">{{ $tipo_avaliacao->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label for="">Percentagem</label>
            <input type="number" name="percentage" id="" class="form-control" min="0" max="100" required>
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-success mb-3 mt-2">
        <i class="fas fa-plus-circle"></i>
        Criar
    </button>
    {!! Form::close() !!}
@endsection
