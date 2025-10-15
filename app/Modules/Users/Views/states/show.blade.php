<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Visualizar Estado')
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
    <li class="breadcrumb-item active" aria-current="page">Visualizar</li>
@endsection
@section('body')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="form-group col-6">
                            <label for="initials" class="col-form-label">Sigla:</label>
                            <input type="text" class="form-control" id="initials" name="initials"
                                value="{{ $state->initials }}" disabled>
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-6">
                            <label for="name" class="col-form-label">Nome:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $state->name }}" disabled>
                        </div>
                        <div class="form-group col-6">
                            <label for="states-type" class="col-form-label">Tipo de estado:</label>
                            <input type="text" class="form-control" id="type" name="type"
                                value="{{ $state->type }}" disabled>
                        </div>
                    </div>
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
