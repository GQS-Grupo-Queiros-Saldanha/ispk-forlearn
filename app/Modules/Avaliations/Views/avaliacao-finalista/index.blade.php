<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'AVALIAÇÃO DE FINALISTA')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lançamento de nota finalista</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')

    <form action="{{ route('nota.avaliacaoFinalista') }}" method="POST">
        @csrf
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
            <div class="col-6">
                <label>Curso(s)</label>
                {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required' => 'required', 'id' => 'courseID']) }}
            </div>
            <div class="col-6">
                <label>Metrica</label>
                <select required name="metrica" class="bg-tran form-control" id="id_metrica">
                    @foreach ($getMetrica as $item)
                        <option value="{{ $item->id_metrica }}">{{ $item->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <table class="table table-hover dark mt-4">
            <thead>
                <th>#</th>
                <th>CONFIRMAÇÃO MATRÍCULA</th>
                <th>ESTUDANTE</th>
                <th>NOTA</th>
            </thead>
            <tbody id="students"> </tbody>
        </table>
        <input type="hidden" id="lectiveY" value="" name="anoLectivo">
        <a href="http://"></a>
        <div class="float-right" id="group_btnSubmit" hidden>
            <button type="submit" class="btn btn-success mb-3">
                <i class="fas fa-plus-circle"></i>
                <span>Guardar</span>
            </button>
        </div>
    </form>
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            var id_curso = $('#courseID');
            var id_anolectivo = $('#lective_year').val();
            var id_metrica = $("#id_metrica").val();
            var lective_year = $("#lective_year");

            // get for curso
            $("#courseID").change(function() {
                id_curso = $(this).val();
                getStudent_finalista();

            });
            //get for year
            lective_year.change(function() {
                id_anolectivo = $(this).val();
                getStudent_finalista();
                getMetricasByYearLective();
            })

            $("#id_metrica").change(function(e) {
                id_metrica = $(this).val();
                getStudent_finalista()
            });

            function getMetricasByYearLective() {
                $.ajax({
                    url: "avaliacao-getMetrica/" + id_anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: true,
                    dataType: 'json',
                    success: function(result) {
                        let metricaClass = $("#id_metrica");
                        if (metricaClass.length == 1) {
                            let html = "";
                            let metrica = metricaClass[0];
                            result.forEach(item => {
                                html +=
                                    `<option value="${item.id_metrica}">${item.nome}</option>`;
                            });
                            metrica.innerHTML = html;
                        }
                    }
                });
            }

            function getStudent_finalista() {
                $("#students").empty();
                var bodyData = '';
                id_metrica = $("#id_metrica").val();
                $.ajax({
                    url: "avaliacao-getFinalistas/" + id_curso + '/' + id_anolectivo + '/' + id_metrica,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if ($("#studentID").val() == "") {
                            return false;
                        }
                    },
                    success: function(result) {
                        if (result.data.length > 0) {
                            $("#students").empty();
                            var i = 1;
                            // var notaP;
                            $.each(result.data, function(index, row) {
                                bodyData += '<tr>'
                                bodyData += "<td class='text-center'>" + i++ + "</td>";
                                bodyData += "<td class='text-center'>" + row
                                    .num_confirmaMatricula + "</td>";
                                bodyData += "<td class=''>" + row.name_student + "</td>";
                                bodyData +=
                                    "<td class='text-center '><input type='number' value=" +
                                    row.nota + "  min='0' class='form-control w-auto' name='nota[" +
                                    row.user_id + '/' + row.id_avaliacao +
                                    "][]' max='20'  required step='any'></td>";
                                bodyData += '</tr>'
                            });
                            $("#group_btnSubmit").attr('hidden', false);
                        } else {
                            bodyData += '<tr>'
                            bodyData +=
                                "<td colspan='4' class='text-center '>Nenhuma estudante finalista.</td>";
                            bodyData += '</tr>'
                            $("#group_btnSubmit").attr('hidden', true);
                        }
                        $("#students").append(bodyData);
                    },
                    error: function(dataResult) {
                        bodyData += '<tr>'
                        bodyData +=
                            "<td colspan='4' class='text-center'>Nenhuma estudante finalista.</td>";
                        bodyData += '</tr>'
                        $("#group_btnSubmit").attr('hidden', true);
                    }
                });
            }
        });
    </script>
@endsection