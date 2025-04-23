@extends('layouts.generic_index_new')
@section('title', __('Gerar lista de pendentes'))
@section('page-title', 'Gerar lista de pendentes')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('requests.index') }}" class="">
        Tesouraria
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">Lista de pendentes</li>
@endsection
@section('selects')
<div class="">
    <label for="lective_years">Selecione o ano lectivo</label>
    <select name="lective_year" id="lective_years" class="selectpicker form-control form-control-sm">
        <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
        @foreach ($lectiveYears as $lectiveYear)
        @if ($lectiveYearSelected == $lectiveYear->id)
        <option value='{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . date('Y-m-d') . ',' . $lectiveYear->currentTranslation->display_name }}"'
            key="{{$lectiveYear->id}}" selected>
            {{ $lectiveYear->currentTranslation->display_name }}
        </option>
        @else
        <option value="{{ $lectiveYear->id . ',' . $lectiveYear->start_date . ',' . $lectiveYear->end_date . ',' . $lectiveYear->currentTranslation->display_name }}"
            key="{{$lectiveYear->id}}">
            {{ $lectiveYear->currentTranslation->display_name }}
        </option>
        @endif
        @endforeach
        <option style="border-top: black 1px solid" value="todosEmolument">
            TODOS
        </option>
    </select>
</div>
@endsection
@section('body')
<form target="_blank" action="{{ route('send.enrollment-pending-parameters') }}" method="POST">
    @csrf
    <input name="lective_years" id="lective_years_aux" type="hidden" />
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
            <select name="article[]" id="article" multiple class="selectpicker form-control" data-actions-box="true"
                data-selected-text-format="count > 3" data-live-search="true">
                @foreach ($articles as $arti)
                <option value="{{ $arti->id }}">
                    {{ $arti->display_name }} - ({{ $arti->code }})
                </option>
                @endforeach
            </select>
        </div>
        <div hidden id="show-month" class="form-group col-3">
            <label class="">Mês</label>
            <select name="month[]" id="ordeMonth" multiple class="selectpicker form-control" data-actions-box="true"
                data-selected-text-format="count > 3" data-live-search="true">
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
        <div id="show-rule" class="form-group col-3">
            <label class="">Regra(s)</label>
            <select name="rules[]" id="rules" multiple class="selectpicker form-control" data-actions-box="true"
                data-selected-text-format="count > 3" data-live-search="true">
            </select>
        </div>

        <div class="form-group col alert alert-danger" role="alert" id="div_error" hidden>
            <p>Nenhuma regra encontrada!</p>
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

        <div class="form-group col-2">
            <button type="submit" class="btn text-white bg-success" name="submitButton" value="excel">
                <i class="fas fa-file-excel"></i>
                Exportar excel
            </button>
        </div>
        <div class="form-group col-2">
            <button id="send-parameters" type="submit" class="btn text-white bg-dark" name="submitButton"
                value="pdf">
                <i class="fas fa-file-pdf"></i>
                Gerar PDF
            </button>
        </div>
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
        var setMonth = @json($ordem_Month);
        var getMonth = JSON.parse(JSON.stringify(setMonth));

        var setarticle = @json($articles);
        var getarticle = JSON.parse(JSON.stringify(setarticle));
        var month = $("#ordeMonth")
        var article = $("#article");
        var show_month = true;
        var lista_article = [];
        var mes = 0;
        calendario();


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

                    if (found == undefined) {
                        lista_article.splice([index], 1)
                    }
                });
            }


            article_id.length > 0 ? $("#show-month").attr('hidden', false) : $("#show-month").attr(
                'hidden', true);
            lista_article.length > 0 ? $("#show-month").attr('hidden', true) : article_id.length > 0 ?
                $("#show-month").attr('hidden', false) : $("#show-month").attr('hidden', true);




        });

        $("#pro").change(function() {
            data = $("#data").val();
            article_id = $('#article').val();
        });

        $("#course").change(function() {
            course_id = $("#course").val();

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
            $("#lective_years_aux").val(data[0]);
            $("#dataInicio_id,#dataFim_id").attr("min", '' + data[1] + '');
            $("#dataInicio_id").val(data[1]);
            $("#dataFim_id").val(data[2]);
            $("#dataInicio_id,#dataFim_id").attr("max", '' + data[2] + '');
        } else {

        }

    }
    var classes = $("#classes");

    function getClasses(curso) {

        $.ajax({
            url: "/reports/getClasses/" + curso + "/" + $("#lective_years_aux").val(),
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function(data) {

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

        getArticleRules();

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

            if (data) {
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


    var rules = $("#rules");


    function getArticleRules() {

        let lectivo = $("#lective_years").val().split(",")[0];
        mes = $("#ordeMonth").length > 0 ? $('#ordeMonth').val() : [];

        $.ajax({
            url: "/reports/get-article-rules/" + lectivo + "/" + $("#classes").val() + "/" + mes + "/" + $("#article").val(),
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function(data) {

            if (data['data'].length > 0) {
                rules.empty();

                $.each(data['data'], function(index, item) {
                    item = item.split('+')
                    rules.append('<option value="' + item[0] + '">' + item[1] + '</option>')

                });

                rules.attr("disabled", false);
                rules.selectpicker('refresh');

            } else {
                $('#div_error').prop('hidden', false);
                setTimeout(function() {
                    $('#div_error').fadeOut('slow');
                }, 2000);
                rules.empty();
                rules.attr("disabled", true);
                rules.selectpicker('refresh');
                console.log(rules.val())

            }

        });
    }
</script>
@endsection