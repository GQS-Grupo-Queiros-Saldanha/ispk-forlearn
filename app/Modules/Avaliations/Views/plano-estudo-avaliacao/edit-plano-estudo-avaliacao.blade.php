<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'EDITAR PLANO DE ESTUDOS E AVALIAÇÃO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('plano_estudo_avaliacao.index') }}">Plano de estudo e avaliação</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection
@section('body')
    @foreach ($getPEA as $pea)
        {!! Form::open(['route' => ['plano_estudo_avaliacao.update', $pea->pea_id]]) !!}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
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
                <label for="avaliacao_id">Avaliação</label>
                <select name="avaliacao_id" id="avaliacao_id" class="form-control" required>
                    <option value="{{ $pea->avaliacao_id }}">{{ $pea->avaliacao_nome }}</option>
                    @foreach ($avaliacaos as $avaliacao)
                        <option value="{{ $avaliacao->avaliacao_id }}">
                            {{ $avaliacao->avaliacao_nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label for="study_plans_edition_id">Edição de Plano de Estudo</label>
                <select name="study_plans_edition_id" id="study_plans_edition_id" class="form-control" required>
                    <option value="{{ $pea->study_plans_edition_id }}">{{ $pea->spe_display_name }}
                    </option>
                    @foreach ($edicao_plano_estudos as $edicao_plano_estudo)
                        <option value="{{ $edicao_plano_estudo->study_plans_edition_id }}">
                            {{ $edicao_plano_estudo->spe_display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label for="hidden_disc">Disciplina</label>
                <input type="hidden" name="hidden_disc" value="{{ $pea->dc_id }}">
                <select name="discipline_id" id="discipline_id" class="form-control" disabled required>
                    <option value="{{ $pea->dc_id }}">{{ $pea->discipline_name }}</option>
                </select>
            </div>
        </div>
       
        <button type="submit" class="btn btn-success mt-3 mb-3">
            <i class="fas fa-plus-circle"></i>
            <span>Editar</span>
        </button>
        {!! Form::close() !!}
    @endforeach
@endsection
@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {
            var discipline_id = {{ $pea->dc_id }};

            $('#study_plans_edition_id').change(function() {
                var study_plans_edition_id = $(this).children("option:selected").val();

                $("#discipline_id").empty();
                if (study_plans_edition_id == "") {
                    $('#discipline_id').prop('disabled', true);
                    $("#discipline_id").empty();
                } else {
                    $.ajax({
                        url: "/avaliations/study_plans_ajax/" + study_plans_edition_id,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            $("#discipline_id").empty();
                            var resultData = dataResult.data;
                            var bodyData = '';
                            var i = 1;
                            $.each(resultData, function(index, row) {
                                bodyData += "<option value=" + row.discipline_id + ">" +
                                    row.dt_display_name + "</option>";
                            })

                            $("#discipline_id").append(bodyData);
                            $('#discipline_id').prop('disabled', false);
                        },
                        error: function(dataResult) {}
                    });
                }
            });
        })
    </script>
@endsection
