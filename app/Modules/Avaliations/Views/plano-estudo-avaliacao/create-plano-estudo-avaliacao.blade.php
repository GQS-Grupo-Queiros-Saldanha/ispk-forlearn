<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PLANO DE ESTUDOS E AVALIAÇÃO')
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
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
    @include('Avaliations::avaliacao.show-panel-avaliation-button')
    {!! Form::open(['route' => ['plano_estudo_avaliacao.store']]) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"> ×</button>
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
            <label>Avaliação</label>
            <select name="avaliacao_id[]" id="avaliacao_id" multiple class="selectpicker form-control autor"
                data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" required>
                <option value=""></option>
                @foreach ($avaliacaos as $avaliacao)
                    <option value="{{ $avaliacao->avaliacao_id }}">
                        {{ $avaliacao->avaliacao_nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Edição de Plano de Estudo</label>
            <select name="study_plans_edition_id" id="study_plans_edition_id" class="form-control" required>
                <option value=""></option>
                @foreach ($edicao_plano_estudos as $edicao_plano_estudo)
                    <option value="{{ $edicao_plano_estudo->study_plans_edition_id }}">
                        {{ $edicao_plano_estudo->spe_display_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Disciplina</label>
            <select name="discipline_id" id="discipline_id" class="form-control" disabled required> </select>
        </div>
    </div>

    <button type="submit" class="btn btn-success mt-3 mb-3">
        <i class="fas fa-plus-circle"></i>
        <span>Criar</span>
    </button>
    {!! Form::close() !!}
@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#study_plans_edition_id').change(function() {
                var study_plans_edition_id = $(this).children("option:selected").val();

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
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                            $("#discipline_id").empty();
                            console.log(dataResult);
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
                        error: function(dataResult) {
                            // alert('error' + result);
                        }
                    });
                }
            });
        })
    </script>
@endsection
