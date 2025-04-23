<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@switch($action)
    @case('create')
        @section('page-title', __('Payments::payments.create_request'))
    @break
@endswitch
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Cria</li>
@endsection
@section('body')
    {!! Form::open(['route' => ['requests.store'], 'id' => 'article_form']) !!}
    <div class="row">
        <div class="col">
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

            <button style="width: 13pc" data-toggle="modal" data-target="#staticBackdrop" type="submit"
                class="btn btn btn-success mb-3 ml-3 ">
                <i class="fas fa-plus-circle"></i> @lang('Payments::payments.submit')
            </button>

            <input id="anolectivo" type="hidden" value="{{ $seletor }}">

            @if (auth()->user()->can('create-requests-others'))
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('Payments::payments.student')</label>
                                {{-- {{ Form::bsLiveSelect('user', $users, null, ['required', 'placeholder' => '']) }} --}}
                                <select data-live-search="true" name="user" id="user"
                                    class="selectpicker form-control form-control-sm" required>
                                    @foreach ($users as $user)
                                        @if ($user['id'] == $userSelected)
                                            <option value="{{ $user['id'] }}" selected>
                                                {{ $user['display_name'] }}
                                            </option>
                                        @else
                                            <option value="{{ $user['id'] }}">
                                                {{ $user['display_name'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            @endif

            <div id="article-select-container" class="card"
                {{ auth()->user()->can('create-requests-others')? 'hidden': '' }}>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            <label>@lang('Payments::articles.article')</label>
                            {{ Form::bsLiveSelectEmpty('article', [], null, ['required', 'placeholder' => '']) }}
                        </div>
                    </div>
                    <div id="discipline-select-container" class="col-3"
                        {{ auth()->user()->can('create-requests-others')? 'hidden': '' }}>
                        <div class="form-group col">
                            <label>Disciplina</label>
                            <select name="discipline" data-live-search="true" required
                                class="selectpicker form-control form-control-sm" required="" id="discipline"
                                data-actions-box="false" data-selected-text-format="values" tabindex="-98">

                            </select>
                        </div>
                    </div>
                    <div id="discipline_month" class="col-3"
                        {{ auth()->user()->can('create-requests-others')? 'hidden': '' }}>
                        <div class="form-group col">
                            <label><label>@lang('Payments::payments.month')</label></label>
                            <select id="discipline_months" name="listmonth[]" multiple
                                class="selectpicker form-control form-control-sm" data-actions-box="true"
                                data-selected-text-format="count > 3" data-live-search="true" required
                                data-selected-text-format="values" tabindex="-98">
                            </select>
                        </div>
                    </div>

                    <div id="select-year-container" class="col-3" hidden>
                        <div class="form-group col">
                            <label>Ano Lectivo</label>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
                                style="width: 100%; !important">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lective == $lectiveYear->id)
                                        <option style="width: 100%;"
                                            value="{{ $lectiveYear->currentTranslation->display_name }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @else
                                        <option style="width: 100%;"
                                            value="{{ $lectiveYear->currentTranslation->display_name }}">
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="select-month-container" class="col-3" hidden>
                        <div class="form-group col">
                            <label>@lang('Payments::payments.month')</label>
                            <select name="listmonth[]" id="listmonth" multiple
                                class="selectpicker form-control form-control-sm" data-actions-box="true"
                                data-selected-text-format="count > 3" data-live-search="true">

                                @foreach ($ordem_Month as $month)
                                    <option value="{{ $month['id'] }}">
                                        {{ $month['display_name'] }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="article-info" class="card" hidden>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="col">
                            <label>@lang('common.code')</label>
                            <div id="show_article_code">...</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col">
                            <label>@lang('Payments::articles.article')</label>
                            <div id="show_article_name">...
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col">
                            <label>@lang('translations.description')</label>
                            <div id="show_article_description">...
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col">
                            <label>@lang('Payments::articles.base_value')</label>
                            <div>
                                <b id="show_article_value">...</b>
                                Kz
                            </div>
                        </div>
                    </div>
                    <div id="show_article_fees_container" class="col-12" style="display: none;">
                        <div class="col">
                            <label>@lang('Payments::articles.extra_fees.extra_fee')</label>
                            <div id="show_article_fees">...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>
        // variaveis
        var anolectivo = $("#anolectivo").val();
        var articles = null;
        var articleContainer = $('#article-select-container');
        var articleInfoContainer = $('#article-info');
        var yearContainer = $('#select-year-container');
        var selectYear = $('#lective_year');
        var selectMonth = $('#listmonth');
        var monthContainer = $('#select-month-container');
        var selectUser = $('#user');
        var selectArticle = $('#article');
        var disciplineContainer = $('#discipline-select-container');
        var selectDiscipline = $('#discipline');
        var listmonth = $('#listmonth');
        var disciplines = null;
        var tipo_inscriacao = null;
        var dadosArray = [];
        var discipline_month = $('#discipline_month');
        var discipline_months = $('#discipline_months');
        var semestre_disciplina = null;
        var setCode_dev = null;
        // 


        selectYear.on('change', function() {
            var anolectivo = $(this).val();
        })


        function resetDisciplines() {
            disciplineContainer.attr('hidden', true);
        }

        function resetArticles() {
            articleContainer.attr('hidden', true);
            resetArticleInfo()
        }

        function resetArticleInfo() {
            articleInfoContainer.prop('hidden', true);
            yearContainer.attr('hidden', true);
            monthContainer.attr('hidden', true);
        }

        function switchDisciplines(userId) {
            dadosArray = [];
            var article_code = selectArticle.val().split(',')

            if (article_code[1] === 'null') {
                disciplineContainer.attr('hidden', true);
                monthContainer.attr('hidden', true);
                discipline_month.attr('hidden', true);
            } else {
                if (userId) {
                    dadosArray.push(selectArticle.val())
                    dadosArray.push(userId)
                    dadosArray.push(anolectivo)

                    $.ajax({
                        url: '{{ route('requests.ajax_disciplines', 0) }}'.slice(0, -1) + dadosArray,
                    }).done(function(data) {
                        console.log(data)
                        setCode_dev = data['setCode_dev']
                        var resultConsult = data['data'];
                        selectDiscipline.prop('disabled', true);
                        selectDiscipline.empty();
                        if (resultConsult == "N_dis") {
                            disciplineContainer.attr('hidden', true);
                            selectDiscipline.prop('disabled', true);
                        } else {
                            if (resultConsult.length > 0) {
                                selectDiscipline.append('<option selected="" value=""></option>');
                                $.each(resultConsult, function(index, discipline) {
                                    selectDiscipline.append('<option value="' + discipline.id_disciplina +
                                        ',' + discipline.disciplines_code + '">' + "#" + discipline
                                        .disciplines_code + " - " + discipline.nome_disciplina +
                                        '</option>')

                                });
                                disciplines = resultConsult;
                            } else {}
                            disciplineContainer.attr('hidden', false);
                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                        }

                    });
                }
            }
        }

        function switchArticles(userId, anolectivo) {
            if (userId) {
                if (anolectivo == 0) {
                    var dadosArry = userId
                } else {
                    var dadosArry = anolectivo + "," + userId;
                }
                $.ajax({
                    url: '{{ route('requests.ajax_articles', 0) }}'.slice(0, -1) + dadosArry
                }).done(function(response) {
                    selectArticle.empty();
                    if (response.length) {
                        selectArticle.append('<option selected="" value=""></option>');
                        response.forEach(function(article) {
                            selectArticle.append('<option value="' + article.id + ',' + article
                                .id_code_dev + '" data="' + article.current_translation.display_name +
                                '">' + article.current_translation.display_name + '</option>');
                        });

                        articles = response;
                        articleContainer.attr('hidden', false);
                        selectArticle.selectpicker('refresh');
                    } else {
                        resetArticles();
                    }
                });
            } else {
                resetArticles();
            }
        }

        function showCurrentSelectedArticle(element) {
            var selectedArticleIdx = $.inArray(
                parseInt(element.value),
                $.map(articles, function(a) {
                    return a.id;
                })
            );

            if (selectedArticleIdx !== -1) {
                var selectArticle = articles[selectedArticleIdx];

                var yearSelect = yearContainer.find('select');
                var monthSelect = monthContainer.find('select');
                if (selectArticle.monthly_charges.length) {

                    disciplineContainer.attr('hidden', true);
                    monthContainer.removeAttr('hidden', false);
                    yearContainer.removeAttr('hidden', false);

                } else {
                    yearContainer.attr('hidden', true);
                    monthContainer.attr('hidden', true);
                    switchDisciplines(selectUser.val())
                }

                $('#show_article_code').text(selectArticle.code);
                $('#show_article_name').text(selectArticle.current_translation.display_name);
                $('#show_article_description').text(selectArticle.current_translation.description ? selectArticle
                    .current_translation.description : 'N/A');
                $('#show_article_value').text(selectArticle.base_value);

                $('#show_article_fees').html(selectArticle.extraFeesAsText);
                selectArticle.extraFeesAsText !== '' ? $('#show_article_fees_container').show() : $(
                    '#show_article_fees_container').hide();

                articleInfoContainer.prop('hidden', false);
            } else {
                resetArticleInfo()
            }
        }

        $(document).ready(function() {

            @if (auth()->user()->can('create-requests-others'))
                switchArticles(selectUser[0].value, anolectivo);
                selectUser.change(function() {
                    switchArticles(this.value);
                    switchDisciplines(this.value);
                });
            @else
                switchArticles(
                    {{ auth()->user()->hasAnyRole(['student', 'candidado-a-estudante'])? auth()->user()->id: null }}
                    );
            @endif

            selectArticle.change(function() {
                var dados_selectArticle = this.value;
                var explode = /\s*,\s*/;
                var array = dados_selectArticle.split(explode);
                discipline_month.attr('hidden', true);
                showCurrentSelectedArticle(this);
            });

            selectDiscipline.change(function() {
                var dados_selectArticle = this.value;
                var explode = /\s*,\s*/;
                var array = dados_selectArticle.split(explode);
                var semestre = array[1]
                semestre_disciplina = semestre.substr(-3, 1)
                //  if (setCode_dev=="in_fre") {

                $.ajax({
                    url: '{{ route('user_requestsDisciplina.month', 0) }}'.slice(0, -1) +
                        semestre_disciplina
                }).done(function(data) {
                    discipline_month.prop('disabled', true);
                    discipline_months.empty();
                    // discipline_months.append('<option selected="" value=""></option>');
                    console.log(semestre_disciplina);
                    $.each(data['data'], function(index, item) {
                        console.log("1 Semestre- " + semestre_disciplina);
                        if (semestre_disciplina == 1 && item.id >= 10 && item.id <= 12 ||
                            semestre_disciplina == 1 && item.id >= 1 && item.id <= 2) {
                            discipline_months.append('<option value="' + item.id + '">' +
                                item.display_name + '</option>')
                        }
                        if (semestre_disciplina == 2 && item.id >= 3 && item.id <= 7) {
                            console.log("2 Semestre- " + semestre_disciplina);
                            discipline_months.append('<option value="' + item.id + '">' +
                                item.display_name + '</option>')

                        }
                        if (semestre_disciplina != 2 && semestre_disciplina != 1) {
                            console.log("Ano");
                            discipline_months.append('<option value="' + item.id + '">' +
                                item.display_name + '</option>')

                        } else {}

                    });
                    discipline_month.attr('hidden', false);
                    discipline_months.selectpicker('refresh');

                });
                // }
            });

            $('button[type="submit"]').click(function(e) {
                e.preventDefault()
                $('#article_form').submit();
            });
        });

        selectArticle.change(
            function() {
                console.log($(this).val());

                setTimeout(() => {

                    let emol = $("#article-select-container button").attr("title");

                    if (emol == "Propina - Finalista") {
                        monthContainer.attr('hidden', false);
                        yearContainer.attr('hidden', false);
                    }

                }, 2000);
            }
        );
    </script>
@endsection
