@section('title',"Tipo de Estados")
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h1 class="m-0 text-dark">editar Tipo Estado</h1>
                    </div>
                    <div class="col-sm-4">
                       <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('types.index') }}">Tipo de Estados</a></li>
                            <li class="breadcrumb-item active" aria-current="page">editar</li>
                            <li class="breadcrumb-item active" aria-current="page">{{$type->name}}</li>
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
                               {!! Form::open(['route' => ['types.update', $type->id], 'method' => 'put']) !!}
                                <div class="form-group">
                                    <input type="text" name="id" value="{{$type->id}}" hidden>
                                    <label for="name" class="col-form-label">Nome:</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{$type->name}}" required>
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
