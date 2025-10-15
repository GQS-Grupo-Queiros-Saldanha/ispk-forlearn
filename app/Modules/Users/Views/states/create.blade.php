<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Criar Estado')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>    
    <li class="breadcrumb-item">
        <a href="{{ route('states.index') }}">Estados</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(['route' => ['states.store']]) !!}
                    <div class="form-group">
                        <label for="initials" class="col-form-label">Sigla:</label>
                        {{ Form::text('initials', old('initials') ?: null, ['class' => 'form-control']) }}
                        @error('initials')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">Nome:</label>
                        {{ Form::text('name', old('name') ?: null, ['class' => 'form-control']) }}
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="states-type" class="col-form-label">Tipo de estado:</label>
                        {{ Form::bsLiveSelect('states_type', $states_type, old('states_type') ?: null, ['placeholder' => '']) }}
                        @error('states_type')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group float-right">
                        <button type="submit" id="submit" class="btn btn-primary">Salvar</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
