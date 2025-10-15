@switch($action)
    @case('create') @section('title',__('GA::summaries.create_summary')) @break
@case('show') @section('title',__('GA::summaries.summary')) @break
@case('edit') @section('title',__('GA::summaries.edit_summary')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("Lessons::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @if(auth()->user()->hasAnyRole(['student']))
                                {{$summary->translations->first()->display_name}}
                                @else
                                @switch($action)
                                    @case('create') @lang('GA::summaries.create_summary') @break
                                    @case('show') @lang('GA::summaries.summary') @break
                                    @case('edit') @lang('GA::summaries.edit_summary') @break
                                @endswitch
                            @endif
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @if(auth()->user()->hasAnyRole(['student']))

                        @else
                            @switch($action)
                                @case('create') {{ Breadcrumbs::render('summaries.create') }} @break
                                @case('show') {{ Breadcrumbs::render('summaries.show', $summary) }} @break
                                @case('edit') {{ Breadcrumbs::render('summaries.edit', $summary) }} @break
                            @endswitch
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(array('route' => 'summaries.store','files' => true)) !!}
                    @break
                    @case('show')
                    {!! Form::model($summary) !!}
                    @break
                    @case('edit')
                    {!! Form::model($summary, ['route' => ['summaries.update', $summary->id], 'role' => 'form','method' => 'put','files' => true]) !!}
                    @break
                @endswitch

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

                        @switch($action)
                            @case('create')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-plus-circle')
                                @lang('common.create')
                            </button>
                            @break
                            @case('edit')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                                @if(auth()->user()->hasAnyRole(['student']))

                                @else
                                <a href="{{ route('summaries.edit', $summary->id) }}" class="btn btn-sm btn-warning mb-3">
                                    @icon('fas fa-edit')
                                    @lang('common.edit')
                                </a>
                            @endif
                            @break
                        @endswitch

                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <label>@lang('GA::study-plans.study_plan')</label>
                                        @if(in_array($action, ['create'], true))
                                            {{ Form::bsLiveSelect('study_plan', $study_plans, $action === 'create' ? null : $summary->studyPlan->id ?? null, ['required', 'placeholder' => '']) }}
                                        @else
                                            <span>
                                                {{ $summary->studyPlan->currentTranslation->display_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('GA::disciplines.discipline')</label>
                                        @if(in_array($action, ['create'], true))
                                            {{ Form::bsLiveSelectEmpty('discipline', [], null, ['required', 'placeholder' => '', 'disabled']) }}
                                        @else
                                            <span>
                                                {{ "#" . $summary->discipline->code . " - " . $summary->discipline->currentTranslation->display_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('GA::discipline-regimes.discipline_regime')</label>
                                        @if(in_array($action, ['create'], true))
                                            {{ Form::bsLiveSelectEmpty('regime', [], null, ['required', 'placeholder' => '', 'disabled']) }}
                                        @else
                                            <span>
                                                {{ $summary->regime->currentTranslation->display_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($action !== 'create')
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        @if ($action === 'edit')
                                            {{ Form::bsNumber('order', $summary->order, ['required', 'min' => 1, 'step' => 1], ['label' => __('GA::summaries.order')]) }}
                                        @else
                                            <div class="form-group col">
                                                <label>@lang('GA::summaries.order')</label>
                                                <span>
                                                    {{ $summary->order }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if(auth()->user()->hasAnyRole(['student']))
                                        <div class="col-6">
                                            <div class="form-group col">
                                                <label for="">Descrição</label>
                                                <span>
                                                    {{$summary->translations->first()->description}}
                                                </span>
                                            </div>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <hr>
                            @endif

                            @if ($action === 'show')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group col">
                                            <label>@lang('GA::summaries.content')</label>
                                            <span>
                                                {{-- $summary->content --}}
                                                <textarea class="form-control" id="summary-ckeditor" name="text" disabled>
                                                    {{$summary->content}}
                                                </textarea>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Form::bsTextArea('text', $action !== 'create' ? $summary->content : null, ['placeholder' => __('GA::summaries.insert_content'), 'disabled' => $action === 'create', 'required'], ['label' => __('GA::summaries.content')]) --}}
                                @if ($action !== 'create')
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group col">
                                                <label>@lang('GA::summaries.insert_content')</label>
                                                <textarea class="form-control" id="summary-ckeditor" name="text" required>
                                                    {{$summary->content}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                    <div class="col-12">
                                        <div class="form-group col">
                                            <label>@lang('GA::summaries.insert_content')</label>
                                            <textarea class="form-control" id="summary-ckeditor" name="text" required>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif

                                <hr>

                            @if ($action === 'show')
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group col">
                                            <label>Ficheiro (s)</label>                                            
                                            @if ($summary->file == null)
                                                @if ($archive_lenght == 0)
                                                    <a href="#" class="btn btn-primary m-2">
                                                        <i class="fa fa-download"> </i>
                                                        Sem ficheiros
                                                    </a>
                                                @else                                                        
                                                    <button type="button" id="btn_termos" class="btn btn-primary m-2" data-toggle="modal" data-target="#exampleModal">
                                                        <i class="fa fa-download"></i>Abrir
                                                    </button>
                                                @endif
                                            @else
                                                @if ($summary->file != null)
                                                {{-- <a href="{{ storage_path().'/attachment/'.$summary->file }}" class="btn btn-primary m-2">
                                                        <i class="fa fa-download"> Abrir / Baixar</i>
                                                    </a> --}}
                                                    <a onclick="generateSummaryArchive({{$summary->id}})" href="#" class="btn btn-primary m-2">
                                                        <i class="fa fa-download"> </i>
                                                        Abrir / Baixar
                                                    </a>
                                                @else
                                                    {{-- @if ($summaries_archive != null)                                                     --}}
                                                        <a href="#" class="btn btn-primary m-2">
                                                            <i class="fa fa-download"> </i>
                                                            Sem ficheiros
                                                        </a>
                                                    {{-- @else                                                        
                                                        <button type="button" id="btn_termos" class="btn btn-primary m-2" data-toggle="modal" data-target="#exampleModal">
                                                            <i class="fa fa-download"></i>Abrir
                                                        </button>
                                                    @endif --}}
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Abrir / Baixar</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Arquivo</th>
                                                    <th scope="col">Ações</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $i=0;
                                                    @endphp
                                                    @foreach ($summaries_archive as $item)
                                                        <tr>
                                                            <th scope="row">{{++$i}}</th>
                                                            <td>Matérial do sumário</td>
                                                            <td>
                                                                <!-- Button trigger modal -->
                                                                <a href="{{$item->archive}}" target="_blank" class="btn btn-primary m-2">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        
                                        </div>                                    
                                    </div>                            
                                </div>
                            @else

                                @if ($action !== 'create')
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group col">
                                                <label>Anexar ficheiro</label>
                                                <input type="file" name="files" id="file">

                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                    {{-- <div class="col-12">
                                        <div class="form-group col">
                                            <label>Anexar ficheiro</label>
                                            <input type="file" name="files" id="file">
                                        </div>
                                    </div>  --}}

                                    <div class="col-12">                                        
                                        <div class="form-group col">
                                            <label>Anexar ficheiro</label>
                                            <input type="file" name="filenames[]" multiple id="file">                                        
                                        </div>
                                    </div>

                                </div>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @if(auth()->user()->hasAnyRole(['student']))

            @else
            <!-- Translations -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex p-0">
                            <h3 class="card-title p-3">@lang('translations.languages')</h3>
                            <ul class="nav nav-pills ml-auto p-2">
                                @foreach($languages as $language)
                                    <li class="nav-item">
                                        <a class="nav-link @if($language->default) active show @endif"
                                           href="#language{{ $language->id }}"
                                           data-toggle="tab">{{ $language->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                @foreach($languages as $language)
                                    <div class="tab-pane @if($language->default) active show @endif"
                                         id="language{{ $language->id }}">
                                        {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                        {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif

            {!! Form::close() !!}

            <div class="modal fade bd-example-modal-lg" id="docente_studyplan_disciplina" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-light">
                                <h5 class="modal-title" id="exampleModalLabel">ALERTA | Docente disciplina regime</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="float-right">
                                    <h4 class="text-danger" style="font-weight:bold; !important" id="docente_studyplan"> 
                                        {{-- Não existem disciplinas associadas ao docente ({{auth()->user()->name}}) --}}
                                        <br>
                                    </h4>
                                </div>
                                <br>
        
                            </div>
                            <div class="modal-footer" >
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>                                
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'summary-ckeditor');
        @if($action === 'show')
            function generateSummaryArchive(id) {
             //console.log("Ola mundo!");
            var myNewTab = window.open('about:blank', '_blank');
            let route = '{{ route('summary.archive', 0) }}'.slice(0, -1) + id            
            // console.log(route)
            $.ajax({
                method: "GET",
                url: route
            }).done(function (url) {
                myNewTab.location.href = url;
            });
        }
        @endif


        @if($action === 'create')

            var selectStudyPlan = $('#study_plan');
            var selectDiscipline = $('#discipline');
            var selectRegime = $('#regime');
            var inputText = $('#text');

            if (!$.isEmptyObject(selectStudyPlan)) {
                switchDisciplines(selectStudyPlan[0]);
                selectStudyPlan.change(function () {
                    switchDisciplines(this);
                });
            }

            if (!$.isEmptyObject(selectDiscipline)) {
                switchRegimes(selectDiscipline[0]);
                selectDiscipline.change(function () {
                    switchRegimes(this);
                });
            }

            if (!$.isEmptyObject(selectRegime)) {
                enableTextInput(selectRegime[0]);
                selectRegime.change(function () {
                    enableTextInput(this);
                });
            }

            function resetStudyPlan() {
                selectDiscipline.prop("disabled", true);
                selectDiscipline.selectpicker('val', "");
                $("button[data-id='discipline']").addClass("disabled");
                resetDiscipline();
            }

            function resetDiscipline() {
                selectRegime.prop('disabled', true);
                selectRegime.selectpicker('val', "");
                $("button[data-id='regime']").addClass("disabled");
                resetRegime();
            }

            function resetRegime() {
                inputText.prop('disabled', true);
            }

            function switchDisciplines(element) {
                resetStudyPlan();

                var studyPlanId = element.value;

                if (!!studyPlanId) {
                    $.ajax({
                        url: '{{ route('summaries.disciplines-ajax', 0) }}'.slice(0, -1) + studyPlanId
                    }).done(function (response) {
                        if (response.length) {
                            console.log(2535);

                            selectDiscipline.prop('disabled', true);
                            selectDiscipline.empty();

                            selectDiscipline.append('<option selected="" value=""></option>');
                            response.forEach(function (discipline) {
                                selectDiscipline
                                    .append('<option value="' + discipline.id + '">' +"# "+ discipline.code +" - "+ discipline.current_translation.display_name + '</option>');
                            });

                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');

                            switchRegimes(selectDiscipline[0]);
                        } else {
                            console.log(3520);
                            $("#docente_studyplan").text("O Docente não têm disciplina(s) associadas a ele, neste plano de estudo para este ano lectivo académico");                
                            $("#docente_studyplan_disciplina").modal('show');
                            resetStudyPlan();
                        }
                    });
                }
            }

            function switchRegimes(element) {
                resetDiscipline();

                var studyPlanId = selectStudyPlan[0].value;
                var disciplineId = element.value;
                console.log(studyPlanId, disciplineId);

                if (studyPlanId && disciplineId) {
                    $.ajax({
                        url: '{{ route('summaries.discipline-regimes-ajax', [0, 0]) }}'.slice(0, -3) + studyPlanId + "/" + disciplineId
                    }).done(function (response) {
                        if (response.length) {
                            selectRegime.prop('disabled', true);
                            selectRegime.empty();

                            selectRegime.append('<option selected="" value=""></option>');
                            response.forEach(function (regime) {
                                selectRegime
                                    .append('<option value="' + regime.id + '">' + regime.display_name + '</option>');
                            });

                            selectRegime.prop('disabled', false);
                            selectRegime.selectpicker('refresh');

                            enableTextInput(selectRegime[0]);
                        } else {
                            resetDiscipline();
                        }
                    });
                }
            }

            function enableTextInput(element) {
                resetRegime();

                var regimeId = element.value;

                if (regimeId) {
                    inputText.prop('disabled', false);
                }
            }

        @endif
    </script>
@endsection
