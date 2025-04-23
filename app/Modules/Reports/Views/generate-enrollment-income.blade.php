<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Folha de caixa [ Detalhada ]')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Folha de caixa [ detalhada ]</li>
@endsection
@section('selects')
    <div class="">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value='{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . date('Y-m-d') }}' selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . $lectiveYear->end_date }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
            <option style="border-top: black 1px solid" value="todosEmolument">TODOS</option>
        </select>
    </div>
@endsection
@section('body')
    <form target="_blank" action="{{ route('send.enrollment-parameters') }}" method="POST" class="w-100 position-relative">
        @csrf
        <input name="lective_years" id="lective_years_aux" type="hidden" />

        <div class="form-row ">

            <div class="col-6">
                <label for="inputEmail4">Data de início</label>
                <input required type="date" name="data1" class="form-control" id="dataInicio_id"
                    placeholder="Data de transação (de)" value="{{ date('Y-m-d') }}">
            </div>

            <div class="col-6">
                <label for="inputPassword4">Data de fim</label>
                <input type="date" name="data2" class="form-control" id="dataFim_id"
                    placeholder="Data de transação (até)">
            </div>

            <div class="col-6 mt-3">
                <label class="">Emolumento(s)</label>
                <select class="selectpicker form-control" name="article[]" id="article" multiple data-actions-box="true"
                     data-live-search="true">
                    @foreach ($articles as $arti)
                        <option value="{{ $arti->id }}">{{ $arti->display_name }} - ({{ $arti->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 mt-3">
                <label class="">Curso(s)</label>
                {{ Form::bsLiveSelect('curso[]', $courses, null, ['form-control', 'multiple', 'id' => 'course', 'label' => 'Curso(s)']) }}
            </div>

            <div class="col-6 mt-3">
                <label class="">Turma(s):</label>
                <select name="classes[]" id="classes" multiple class="selectpicker form-control"
                    data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" disabled>
                </select>
            </div>


            <div class="col-6 mt-3">
                <label class="">Estudante(s):</label>
                <select name="student[]" id="students" multiple class="selectpicker form-control"
                    data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" disabled>
                </select>
            </div>
        </div>

        <div class="form-row mt-3 mb-3" id="botoes_gerarFolha">
            <div class="form-group col-md-3 border-top">
                <button id="" type="submit" class="btn mt-3"
                    style="background: black;color:white "name="submitButton" value="pdf"><i style="font-size: 1.1pc"
                        class="fas fa-file-pdf"></i> Gerar PDF</button>
            </div>
            <div class="form-group col-md-3 border-top ">
                <button type="submit" class="btn mt-3" style="background: #38b16b;color:white" name="submitButton"
                    value="excel"><i style="font-size: 1.1pc" class="fas fa-file-excel"></i> Gerar excel</button>
            </div>

            <div class="form-group col-md-6"></div>
        </div>

    </form>
@endsection

@section('scripts-new')
    @parent
    <script>
        $(function() {
            var article_id;
            var course_id;
            var data;
            var article = $("#article");
            var classes = $("#classes");
            calendario();
            $("#article").change(function() {
                data = $("#data").val();
                console.log(data);
                article_id = $('#article').val();
                console.log(article_id);
            });



            // Listagem dos estudantes com os emolumentos pagos ( Curso e Emolumentos )


            function getClasses(curso, ano) {
                console.log("Pegando as turmas...");
                $.ajax({
                    url: "/reports/getClasses/" + curso + "/" + ano,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    // console.log(data)
                    if (data['data'].length > 0) {
                        classes.empty();
                        $.each(data['data'], function(index, item) {
                            classes.append('<option value="' + item.id + '">#' + item.code +
                                ' </option>')

                        });
                        classes.attr("disabled", false);
                        classes.selectpicker('refresh');

                    } else {
                        classes.empty();
                        classes.attr("disabled", true);
                        classes.selectpicker('refresh');
                    }

                });
            }

            $("#course").change(function() {
                curso = $("#course").val();
                ano = $("#lective_years").val();
                getClasses(curso, ano);
            });
            classes.change(function() {
                curso = $("#course").val();
                classes = $("#classes").val();

                student = $("#students");
                student.attr("disabled", true);
                $.ajax({
                    url: "/reports/generate-enrollment-incomeStudent/" + curso + "/" + $("#classes").val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    
                    if (data['data'].length > 0) {
                        console.log("entrei",data)
                        student.empty();
                        $.each(data['data'], function(index, item) {
                            student.append('<option value="' + item.id + '"> ' + item
                                .display_name + ' # ' + item.meca + ' ( ' + item.email +
                                ' )</option>')

                        });
                        student.attr("disabled", false);
                        student.selectpicker('refresh');

                    } else {
                        console.log("não entrei",data)
                        student.empty();
                        student.attr("disabled", true);
                        student.selectpicker('refresh');
                    }

                });

            });

            $("#lective_years").change(function() {
                var elemento = $("#lective_years").val().split(",");
                calendario();
                $.ajax({
                    url: "/reports/generate-enrollment-incomeEmulomento/" + elemento[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    console.log(data)
                    if (data['data'].length > 0) {
                        article.empty();
                        $.each(data['data'], function(index, item) {

                            article.append('<option value="' + item.id + '">' + item
                                .display_name + ' ' + item.code + '</option>')

                        });
                        article.selectpicker('refresh');

                    } else {
                        article.empty();
                        article.selectpicker('refresh');
                    }

                });
            })

        });

        function sendParameters(data, article, course) {
            $.ajax({

                url: "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" + course_id + "/" +
                    avaliacao_id + "/" + class_id,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            });
        }



        function calendario() {

            var data = $("#lective_years").val().split(",");
            if (data !== "todosEmolument") {
                $("#lective_years_aux").val(data[0]);
                $("#dataInicio_id,#dataFim_id").attr("min", '' + data[1] + '');
                // $("#dataInicio_id").val(data[1]);
                $("#dataFim_id").val(data[2]);
                $("#dataInicio_id,#dataFim_id").attr("max", '' + data[2] + '');
            } else {

            }

        }
    </script>

@endsection
