<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Editar Estado')
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
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection
@section('body')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(['route' => ['states.update', $state->id], 'method' => 'put']) !!}

                    <div class="row">

                        <div class="form-group col-6">
                            <input type="text" name="id" value="{{ $state->id }}" hidden>
                            <label for="initials" class="col-form-label">Sigla:</label>
                            <input type="text" class="form-control" id="initials" name="initials"
                                value="{{ $state->initials }}">
                            @error('initials')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-6">
                            <label for="name" class="col-form-label">Nome:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $state->name }}">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="states-type" class="col-form-label">Tipo de estado:</label>
                            <select name="states_type" id="states-type" class="form-control" required>
                                <option value="{{ $state->type_id }}">{{ $state->type }}</option>
                            </select>
                            @error('states_type')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group float-right">
                        <button type="submit" id="submit" class="btn btn-success">Salvar</button>
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
@section('scripts')
    @parent
    <script>
        $(function() {
            loadStatesType();

            function loadStatesType() {
                $.ajax({
                    url: "{{ route('states.type') }}",
                    type: "GET",
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        var content = '';
                        data.forEach(function(data) {
                            content += "<option value=" + data.id + ">" + data.name +
                                "</option>"
                        });
                        $("#states-type").append(content);
                    }
                });
            }

        });
    </script>
@endsection
