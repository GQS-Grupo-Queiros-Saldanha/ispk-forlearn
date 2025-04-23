@section('title', __('Lista de não devedores'))


@extends('layouts.backoffice')

@section('content')
    <div class="content-panel" style="padding: 0px">
        @include('Payments::requests.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                           
                          Lista de não devedores
                            
                        </h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">

            <form target="_blank" action="{{ route('send.enrollment-parameters-total') }}" method="POST">
                @csrf
                <div class="row mr-3">
                    <div class="col-md-10">
                    </div>
                    <div class="form-group col-2">
                        <label> Selecione o ano lectivo </label>
                        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm"
                            style="width: 100%; !important">
                            @foreach ($lectiveYears as $lectiveYear)
                                @if ($lectiveYearSelected == $lectiveYear->id)
                                    <option
                                        value='{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . date('Y-m-d') . ',' . $lectiveYear->currentTranslation->display_name }}"'
                                        selected>
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @else
                                    <option
                                        value="{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . $lectiveYear->end_date . ',' . $lectiveYear->currentTranslation->display_name }}">
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @endif
                            @endforeach
                            <option style="border-top: black 1px solid" value="todosEmolument">
                                TODOS
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row ml-2">
                    <div class="form-group col-3">
                        {{ Form::bsDate('dataInicio_id', null, ['placeholder' => 'Data de transação (de)', 'required', 'value' => date('Y-m-d')], ['label' => 'Data de transação (de)']) }}
                    </div>
                    <div class="form-group col-3" style="margin-left: 22px">
                        {{ Form::bsDate('dataFim_id', null, ['placeholder' => 'Data de transação (até)', 'value' => date('Y-m-d')], ['label' => 'Data de transação (até)']) }}
                    </div>
                </div>
                <div class="row ml-4">
                    <div class="form-group col-6">
                        <label class="">Emolumento(s)</label>
                        <select name="article[]" id="article" multiple class="selectpicker form-control"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($articles as $arti)
                                <option value="{{ $arti->id }}">
                                    {{ $arti->display_name }} - ({{ $arti->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div hidden id="show-month" class="form-group col-3">
                        <label class="">Mês</label>
                        <select name="month[]" id="ordeMonth" multiple class="selectpicker form-control"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($ordem_Month as $month)
                                <option value="{{ $month['id'] }}">
                                    {{ $month['display_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row ml-4">
                    <div class="col-md-6">
                        <label class="">Curso(s)</label>
                        {{ Form::bsLiveSelect('curso[]', $courses, null, ['multiple', 'id' => 'course', 'label' => 'Curso(s)']) }}
                    </div>
                    <div class="col-3"> 
                          <label class="">Turma(s):</label>
                        <select name="classes[]" id="classes" multiple class="selectpicker form-control  form-control-sm"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" disabled>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row ml-4">
                    <div class="form-group col-6">
                        <label class="mb-2">Estudante(s):</label>
                        <select name="student[]" id="students" multiple class="selectpicker form-control form-control-sm"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            {{-- @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{$student->display_name . " #" . $student->meca . " (" . $student->email .")" }}
                        </option>
                        @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="row ml-4">

                </div>

                <div class="row mr-3"> 
                    
                    <div class="form-group col-8">
                    </div>
                    
                    <div class="form-group col-2" >
                        <button hidden type="submit" class="btn text-white bg-success" name="submitButton" name="submitButton"
                            value="excel">
                            <i class="fas fa-file"></i>
                            Exportar excel
                        </button>
                    </div>
                    <div class="form-group col-2">
                        <button id="send-parameters" type="submit" class="btn text-white bg-dark" name="submitButton"
                            value="pdf">
                            <i class="fas fa-file"></i>
                            Gerar PDF
                        </button>
                    </div>
                </div>
                
                <br>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            var article_id;
            var course_id;
            var data;
            var setMonth = @json($ordem_Month);
            var getMonth = JSON.parse(JSON.stringify(setMonth));

            var setarticle = @json($articles);
            var getarticle = JSON.parse(JSON.stringify(setarticle));
            var month = $("#ordeMonth")
            var article = $("#article");
            var show_month = true;
            var lista_article = [];
            calendario();
            // console.log(getarticle);

            function getOrganizacion() {

            }
            $("#article").change(function(e) {
                article_id = $('#article').val();
                $.each(article_id, function(index, item) {
                    $.each(getarticle, function(key, element) {
                        if (element.id == item && element.article_id_extra_fees == null) {
                            show_month = false
                            lista_article.push({
                                id: item,
                                show_month: false
                            })
                        }
                    });
                });
                if (lista_article.lengt > 0) {
                    $.each(lista_article, function(index, item) {
                        var found = article_id.find(element => element == item.id)
                        console.log(found)
                        if (found == undefined) {
                            lista_article.splice([index], 1)
                        }
                    });
                }


                article_id.length > 0 ? $("#show-month").attr('hidden', false) : $("#show-month").attr(
                    'hidden', true);
                lista_article.length > 0 ? $("#show-month").attr('hidden', true) : article_id.length > 0 ?
                    $("#show-month").attr('hidden', false) : $("#show-month").attr('hidden', true);


                console.log(article_id);
                console.log(lista_article);

            });

            $("#pro").change(function() {
                data = $("#data").val();
                article_id = $('#article').val();
            });

            $("#course").change(function() {
                course_id = $("#course").val();
                // console.log(course_id);
                getClasses(course_id);
            });

            $("#lective_years").change(function() {
                var elemento = $("#lective_years").val().split(",");
                if (elemento[3] == "20/21") {
                    month.empty();
                    month.append('<option value="3_2020">Março</option>')
                    $.each(getMonth, function(index, element) {
                        month.append('<option value="' + element.id + '">' + element.display_name +
                            '</option>')
                    });
                    month.selectpicker('refresh');

                } else {
                    month.empty();
                    $.each(getMonth, function(index, element) {
                        month.append('<option value="' + element.id + '">' + element.display_name +
                            '</option>')
                    });
                    month.selectpicker('refresh');
                }
                console.log(elemento[3])
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
                    var monthOut_Dezembro = [];

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
                $("#dataInicio_id,#dataFim_id").attr("min", '' + data[1] + '');
                $("#dataInicio_id").val(data[1]);
                $("#dataFim_id").val(data[2]);
                $("#dataInicio_id,#dataFim_id").attr("max", '' + data[2] + '');
            } else {

            }

        }
        var classes = $("#classes");

        function getClasses(curso) {
            console.log("Pegando as turmas...");
            $.ajax({
                url: "/reports/getClasses/" + curso,
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
                        classes.append('<option value="' + item.id + '">#' + item.code + ' </option>')

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
        classes.change(function() {
            curso = $("#course").val();
            classes = $("#classes").val();

            student = $("#students");
            student.attr("disabled", true);
            student.empty();
            $.ajax({
                url: "/reports/generate-enrollment-incomeStudent/" + curso + "/" + classes,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                // console.log(data)
                if (data['data'].length > 0) {
                    student.empty();
                    $.each(data['data'], function(index, item) {
                        student.append('<option value="' + item.id + '"> ' + item.display_name +
                            ' # ' + item.meca + ' ( ' + item.email + ' )</option>')

                    });
                    student.attr("disabled", false);
                    student.selectpicker('refresh');

                } else {
                    student.empty();
                    student.attr("disabled", true);
                    student.selectpicker('refresh');
                }

            });

        });
    </script>
@endsection
