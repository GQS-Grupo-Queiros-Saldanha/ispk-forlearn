@section('title',"Tipos de Estados")
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-9">
                        <h1 class="m-0 text-dark">Criar tipo de estado</h1>
                    </div>
                    <div class="col-sm-3">
                       <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('types.index') }}">Tipos de estados</a></li>
                                <li class="breadcrumb-item active" aria-current="page">criar</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                               {!! Form::open(['route' => ['types.store']]) !!}
                                    <div class="form-group">
                                        <label for="name" class="col-form-label">Nome:</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group float-right">
                                        <button type="submit" id="submit" class="btn btn-success">Salvar</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
  
@endsection
