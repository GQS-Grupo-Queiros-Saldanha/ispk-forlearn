@switch($action)
@case('create') @section('title',__('GA::study-plan-editions.create_study_plan_edition')) @break
@case('show') @section('title',__('GA::study-plan-editions.study_plan_edition')) @break
@case('edit') @section('title',__('GA::study-plan-editions.edit_study_plan_edition')) @break
@endswitch 

@extends('layouts.backoffice')

@section('content')

    <!--suppress VueDuplicateTag -->
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <!--- TITULO --->
						<h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('GA::study-plan-editions.create_study_plan_edition') @break
                                @case('show') @lang('GA::study-plan-editions.study_plan_edition') @break
                                @case('edit') @lang('GA::study-plan-editions.edit_study_plan_edition')  @break
                            @endswitch
                        </h1>
                    </div>
					<!--- BOTÃO --->
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('study-plan-editions.create') }} @break
                            @case('show') {{ Breadcrumbs::render('study-plan-editions.show', $study_plan_edition) }} @break
                            @case('edit') {{ Breadcrumbs::render('study-plan-editions.edit', $study_plan_edition) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['study-plan-editions.store']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($study_plan_edition) !!}
                    @break
                    @case('edit')
                    {!! Form::model($study_plan_edition, ['route' => ['study-plan-editions.update', $study_plan_edition->id], 'method' => 'put']) !!}
                    @break
                @endswitch
                <div class="row">
                    <div class="col">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button  type="button" class="close" data-dismiss="alert" aria-hidden="true">
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
                            <a href="{{ route('study-plan-editions.edit', $study_plan_edition->id) }}"
                               class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card-body">
                                <div class="select-study-plan row">
                                    @include('GA::study-plan-editions.partials.study-plans')
                                </div>
                                <div class="row spe-form">

                                    <div class="col-6">
                                        {{ Form::bsDate('start_date', null, ['placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.start_date')]) }}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::bsDate('end_date', null, ['placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.end_date')]) }}
                                    </div>

                                    <div class="col-6">
                                        @include('GA::study-plan-editions.partials.lective-years')
                                    </div>

                                    <div class="d-none col-6">
                                        @include('GA::study-plan-editions.partials.year-transition-rules')
                                    </div>

                                    <div class="col-6">
                                      
                                        @include('GA::study-plan-editions.partials.period-types')
                                    </div>

                                    <div class="d-none col-6">
                                        {{ Form::bsNumber('max_enrollments', null, ['placeholder' => __('GA::study-plan-editions.max_enrollments'), 'disabled' => $action === 'show', ], ['label' => __('GA::study-plan-editions.max_enrollments')]) }}
                                    </div>
                                    <div class="d-none col-6">
                                        {{ Form::bsCheckbox('block_enrollments', null, $action === 'edit' || $action === 'show' ? $study_plan_edition->block_enrollments : '0', ['disabled' => $action === 'show'], ['label' => __('GA::study-plan-editions.block_enrollments')]) }}
                                    </div>
                                    <div class="d-none col-6">
                                        @include('GA::study-plan-editions.partials.average-calculation-rules')
                                    </div>
                                    <div class="d-none col-6">
                                        <div class="form-group col">
                                            <label for="max_enrollments">@lang('GA::study-plan-editions.access_types')
                                            </label>
                                            <div id="access_types"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
							<div class="card spe-form">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        @lang('GA::disciplines.disciplines')</h5>
										<!--- MOSTRA AS DISCIPLINAS --->
                                    <table id="discipline_regimes" data-display-length='100'class="table table-striped table-hover">


                                    </table>
                                </div>
                            </div>
                            
                            <div id="precedences-container" class="card spe-form" hidden >
                                <div class="card-body">
                                    {{-- <h5 class="card-title mb-3">@lang('GA::study-plan-editions.precedences')</h5>
                                    @if($action === 'edit' || $action === 'create')
                                        <button data-toggle='modal' type='button' data-type='add'
                                                data-target='#modal_type_5'
                                                class='btn btn-sm btn-success mb-3'
                                                id="create-precedences">
                                                @icon('fas fa-plus')
                                        </button>
                                    @endif --}}
                                    <div id="discipline_precedence"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                           href="#language{{ $language->id }}" data-toggle="tab">{{ $language->name }}
                                        </a>
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
                                        {{ Form::bsText('abbreviation['.$language->id.']', $action === 'create' ? old('abbreviation.'.$language->id) : $translations[$language->id]['abbreviation'] ?? null, ['placeholder' => __('translations.abbreviation'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.abbreviation')]) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    @include('layouts.backoffice.modals.modal_type_5')
    @include('GA::study-plan-editions.partials.modalAvaliacao')
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/backoffice/dropdown-extra-fields.js') }}"></script>
    <script>
        var action = '{!! $action !!}';
        var dt3;
        var studyPlan = null;
        var studyPlanYears = null;
        var studyPlanDisciplinesYear = null;
        var studyPlanDisciplinesPreviousYears = null;
        var datatableDisciplines = null;
        var dataSet = null;

        var selectStudyPlan = $('select[name=study_plan]');
        var selectYear = $('select[name=course_year]');
        var selectDisciplinePrecendence = $('select[name=discipline_precedence]');
        var selectPrecendencePrecendence = $('select[name=precedence_precedence]');

        function resetStudyPlan() {
            $('.spe-form').removeClass('show');
            selectYear.prop("disabled", true);
            $("button[data-id='study_plan_edition_year']").addClass("disabled");
            selectYear.selectpicker('val', "");
        }

        function loadStudyPlanData(value, year) {
           
            if (value) {
                let route = ("{{ route('study-plans.fetch','id_study_plan') }}").replace('id_study_plan', value);
                $.get(route, function (data) {
                    studyPlan = data;
                    @if ($action === 'create')
                    loadCourseYears();
                    @endif

                    if (year) {
                        loadStudyPlanInputs(year);
                    }
                });
            }
            resetStudyPlan();
        }

        function loadCourseYears() {
            studyPlanYears = studyPlan.study_plans_has_disciplines
                .map(d => d.years)
                .filter((year, index, arr) => arr.indexOf(year) == index);

            if (studyPlanYears.length) {
                selectYear.prop('disabled', true);
                selectYear.empty();

                selectYear.append('<option selected="" value=""></option>');
                studyPlanYears.forEach(function (year) {
                    selectYear.append('<option value="' + year + '">' + year + '</option>');
                });

                selectYear.prop('disabled', false);
                selectYear.selectpicker('refresh');
            } else {
                resetStudyPlan();
            }
        }

        function loadStudyPlanInputs(year) {
            console.log('oi');
            $('.spe-form').addClass('show');

            studyPlanDisciplinesYear = [];
            studyPlanDisciplinesPreviousYears = [];
            $.each(studyPlan.study_plans_has_disciplines, function (k, v) {
                if (parseInt(v.years) === parseInt(year)) {
                    studyPlanDisciplinesYear.push(v)
                }
                if (parseInt(v.years) < parseInt(year)) {
                    studyPlanDisciplinesPreviousYears.push(v)
                }
            });

            function SortByName(a, b) {
                var aName = "#" + a.discipline.code + " - " + a.discipline.current_translation.display_name.toLowerCase();
                var bName = "#" + b.discipline.code + " - " + b.discipline.current_translation.display_name.toLowerCase();
                return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
            }

            studyPlanDisciplinesYear.sort(SortByName);
            studyPlanDisciplinesPreviousYears.sort(SortByName);

            @if ($action === 'create')
            loadDisciplineRegimes();
            @else
            buildDataTableDisciplines(loadDisciplines());
            @endif

            loadPrecedences();
            // Load selects
            $('.selectpicker').selectpicker();
        }

        function loadAccessTypes() {
            //try {
            // Get all access types
            let accessTypes = JSON.parse('{!! json_encode($access_types) !!}');

            // Get SPE access types
                @if($action !== 'create')
            let studyPlanAccessTypes = JSON.parse('{!! json_encode($study_plan_edition->accessTypes) !!}');
            @endif

            // Options
            let options = [];
            if (typeof accessTypes === 'object') {
                $.each(accessTypes, function (k, v) {
                    let option = {
                        checkbox: {
                            attributes: [{
                                key: 'value',
                                value: v.id
                            }, {
                                key: 'text',
                                value: v.translation.description
                            }, {
                                key: 'name',
                                value: 'access_types[' + k + '][access_type_id]'
                            }],
                            label: v.translation.description
                        },
                        input: {
                            attributes: [{
                                key: 'type',
                                value: 'number'
                            }, {
                                key: 'name',
                                value: 'access_types[' + k + '][max_enrollments]'
                            }, {
                                key: 'required',
                                value: 'required'
                            }, {
                                key: 'placeholder',
                                value: '@lang('GA::access-types.max_enrollments')'
                            }]
                        }
                    };

                    // Check for values
                        @if ($action !== 'create')
                    let found = studyPlanAccessTypes.find(o => o.access_type_id === v.id);
                    if (typeof found !== 'undefined') {
                        option.checkbox.attributes.push({
                            key: 'checked',
                            value: 'checked'
                        });
                        option.input.attributes.push({
                            key: 'value',
                            value: found.max_enrollments
                        });
                    }
                        @if($action === 'show')
                    let disabled = {
                            key: 'disabled',
                            value: 'disabled'
                        };
                    option.checkbox.attributes.push(disabled);
                    option.input.attributes.push(disabled);
                    @endif
                    @endif

                    options.push(option);
                });

                let data = {
                    text: '@lang('GA::study-plan-editions.access_types')',
                    options: options
                };

                $('#access_types').DropdownExtra(data).on('changed', function (e, isOpen) {
                });

                @if($action !== 'create')
                $('#access_types > div').trigger('click');
                @endif

            }
            /*}catch (e) {
                // console.error(e);
           }*/
        }

        function loadPrecedences() {
            var container = $('#precedences-container');

            if (studyPlanDisciplinesYear.length && studyPlanDisciplinesPreviousYears.length) {
                /***********************************
                 * STUDY PLAN EDITIONS -  PRECEDENCES
                 ************************************/

                var initialData = [];

                @if($action === 'edit' || $action === 'show')

                // create dynamic list with initial data
                var data = {!! json_encode($study_plan_edition->precedences) !!}

                $.each(data, function (k, v) {
                    var auxArray = [{
                        text: '#' + v.discipline.code + ' - ' + v.discipline.translation.description,
                        value: v.discipline.id,
                        name: 'discipline_precedence[]'
                    }, {
                        text: '#' + v.parent.code + ' - ' + v.parent.translation.description,
                        value: v.parent.id,
                        name: 'precedence_precedence[]'
                    }];
                    initialData.push(auxArray);
                });
                @else

                // stop default behavior of keeping data in localStorage
                localStorage.removeItem('#discipline_precedence');

                if (typeof localStorage.getItem('#table_discipline_precedence') !== 'undefined') {
                    // $('#table_discipline_precedence').html(localStorage.getItem('#table_discipline_precedence'));
                    // localStorage.removeItem('#table_discipline_precedence');
                }

                @endif

                // Creates dynamic datatable object
                dt3 = new DynamicDatatable('#discipline_precedence', 'table_discipline_precedence', [{
                    text: "@lang('GA::study-plan-editions.discipline_precedence')",
                    name: 'discipline_precedence[]'
                }, {
                    text: "@lang('GA::study-plan-editions.precedence_precedence')",
                    name: 'precedence_precedence[]'
                }], initialData, 'Eliminar', 'modal_type_5', "{!! $action !!}");
                dt3.initialize();

                container.prop('hidden', false);
            } else {
                container.prop('hidden', true);
            }
        }

        function loadPrecedents() {
            if (studyPlanDisciplinesYear.length && studyPlanDisciplinesPreviousYears.length) {
                selectDisciplinePrecendence.prop('disabled', true);
                selectDisciplinePrecendence.empty();

                selectPrecendencePrecendence.prop('disabled', true);
                selectPrecendencePrecendence.empty();

                selectDisciplinePrecendence.append('<option selected="" value=""></option>');
                studyPlanDisciplinesYear.forEach(function (d) {
                    var display_name = "#" + d.discipline.code + " - " + d.discipline.current_translation.display_name;
                    selectDisciplinePrecendence
                        .append('<option value="' + d.discipline.id + '">' + display_name + '</option>');
                });

                selectPrecendencePrecendence.append('<option selected="" value=""></option>');
                studyPlanDisciplinesPreviousYears.forEach(function (d) {
                    var display_name = "#" + d.discipline.code + " - " + d.discipline.current_translation.display_name;
                    selectPrecendencePrecendence
                        .append('<option value="' + d.discipline.id + '">' + display_name + '</option>');
                });

                selectDisciplinePrecendence.prop('disabled', false);
                selectDisciplinePrecendence.selectpicker('refresh');

                selectPrecendencePrecendence.prop('disabled', false);
                selectPrecendencePrecendence.selectpicker('refresh');
            } else {
                resetPrecedence();
            }
        }

        function loadDisciplineRegimes() {
            dataSet = [];

            // # ACTION # CREATE ##
            @if($action === 'create')
            $.each(studyPlanDisciplinesYear, function (k, v) {
                let auxArray = [
                    SimpleHTMLElement('input', {
                        type: 'checkbox',
                        name: 'dr_checked_disciplines[][id]',
                        value: v.discipline.id,
                        checked: true
                    }),
                    '#' + v.discipline.code + ' - ' + v.discipline.current_translation.display_name + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_disciplines[][id]',
                        value: v.discipline.id
                    }),
                    v.years + SimpleHTMLElement('input', {type: 'hidden', name: 'dr_years[]', value: v.years}),
                    v.total_hours + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_total_hours[]',
                        value: v.total_hours
                    }),
					//MOSTRA DADOS NOS ESTES CAMPOS # CRIAR DADOS                    
					v.area + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_area[]',
                        value: v.area
                    }),
					v.avaliacao + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_avaliacao[]',
                        value: v.avaliacao
                    })
                ];
                dataSet.push(auxArray);
            });
            @endif

            buildDataTableDisciplines(dataSet);
        }

        function buildDataTableDisciplines(dataSet) {
            if (datatableDisciplines != null) {
                datatableDisciplines.destroy();
            }


            datatableDisciplines = $('#discipline_regimes').DataTable({
                processing: false,
                serverSide: false,
                data: dataSet,
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [
                        @if($action !== 'show')
                    {
                        title: "@lang('common.active')"
                    },
                        @endif
                    {
                        title: "@lang('GA::disciplines.disciplines')"
                    },
                    {title: "@lang('common.year')"},
                    {title: "@lang('GA::disciplines.total_hours')"},
					// MOSTRA ESTES CAMPOS
					{title: "ÁREA"},
					{title: "AVALIAÇÕES"},
                    // {title: "LIMITE FALTAS"}
			
                ],
                    "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    }
            });
        }

        @if ($action !== 'create')
        function loadDisciplines() {
            dataSet = []; 
           
            var studyPlanEdition = @json($study_plan_edition);
            var avaliacaoPlanos = JSON.parse('{!! json_encode($avaliacaoPlanos) !!}');
            var falta_disciplina = JSON.parse('{!! json_encode($falta_disciplina) !!}');
            // console.log(falta_disciplina);
            @if ($action === 'show')
            loadDisciplinesShow(studyPlanEdition,avaliacaoPlanos,falta_disciplina);
            @elseif($action === 'edit')
            loadDisciplinesEdit(studyPlanEdition,falta_disciplina);
            @endif
           
                return dataSet;
        }

        @endif



        function loadDisciplinesShow(studyPlanEdition,avaliacaoPlanos,falta_disciplina) {
                var estudo=null;
                var falatDisc=null;
                
                $.each(studyPlanEdition.disciplines,  function (k, v) {

                var studyPlanDisciplineData = v.study_plans_has_disciplines.filter(function (d) {
                    return d.study_plans_id = studyPlanEdition.study_plans_id;
                });


                if (studyPlanDisciplineData.length) {
                    
                    // if(avaliacaoPlanos.length){
                    if(avaliacaoPlanos[v.id]){
                       estudo=avaliacaoPlanos[v.id][0];
                    }
                    else{
                        estudo="Sem Avaliação";
                     }

                     
                     
                     if (falta_disciplina.length == 0) {
                            falatDisc="0";  
                        }else{
                        falta_disciplina.find(object =>{
                            if(object.disciplines_id == [v.id] && object.id_plain_study == studyPlanEdition.id  ){
                                falatDisc=object.number_falt;
                                console.log([v.id]," id_disc ", object.disciplines_id ," falta ", object.number_falt);

                                return  falatDisc=object.number_falt;
                            }
                            else{
                                falatDisc="0";
                            }

                        });
                      }
                    var auxArray = [
                        '#' + v.code + ' - ' + v.current_translation.display_name + SimpleHTMLElement('input', {
                            type: 'hidden',
                            name: 'dr_disciplines[][id]',
                            value: v.id
                        }),
                        studyPlanDisciplineData[0].years + SimpleHTMLElement('input', {
                            type: 'hidden',
                            name: 'dr_years[]',
                            value: studyPlanDisciplineData[0].years
                        }),
                        studyPlanDisciplineData[0].total_hours + SimpleHTMLElement('input', {
                            type: 'hidden',
                            name: 'dr_total_hours[]',
                            value: studyPlanDisciplineData[0].total_hours
                        }),
						    //MOSTRA DADOS NOS ESTES CAMPOS # EDITAR DADOS
                        v.discipline_areas[0].translations[0].display_name  + SimpleHTMLElement('input', {
                            type: 'hidden',
                            name: 'dr_area[]',
                            value: v.discipline_areas[0].translations[0].display_name 
                        }),
                          estudo + SimpleHTMLElement('input' , {
                            type: 'hidden',
                            name: 'dr_avaliacao[]',
                            value: estudo
                        })
                        
                        // ,
                        // falatDisc  + SimpleHTMLElement('input', {
                        //     type: 'hidden',
                        //     name: 'dr_falta[]',
                        //     value:falatDisc
                        // })
                       
                    ];
                    dataSet.push(auxArray);
                }


            });
        }

      






        function loadDisciplinesEdit(studyPlanEdition,falta_disciplina) {
            
            var count; 
            var falatDisc=null;
            var dados={
                    id_discipl:null,
                    falta:null
                }

            $.each(studyPlanDisciplinesYear, function (k, v) {
                var auxArray = [];
                     count++;

                var studyPlanDisciplines = studyPlanEdition.disciplines.map(d => d.id);
                
         
                // var studyPlanDisciplinesArea = studyPlanEdition.disciplines[k].discipline_areas.map(g => g.code);

                if (falta_disciplina.length == 0) {
                    falatDisc="0";  
                }else{
                falta_disciplina.find(objet =>{
                    console.log(objet);
                        if (objet==null) {
                            falatDisc="0";  
                        }
                        if(objet.disciplines_id == [v.discipline.id] &&  objet.id_plain_study == studyPlanEdition.id){
                            falatDisc=objet.number_falt;
                            return  falatDisc=objet.number_falt;
                        }
                        else{
                            falatDisc="0";
                        }

                    });
                }
                 

                var checkboxAttrs = {
                    type: 'checkbox',
                    name: 'dr_checked_disciplines[][id]',
                    value: v.discipline.id
                };
                if ($.inArray(v.disciplines_id, studyPlanDisciplines) !== -1) {
                    checkboxAttrs['checked'] = true;
                }


            
                var checkbox = SimpleHTMLElement('input', checkboxAttrs);
                auxArray.push(checkbox);
               
                auxArray = auxArray.concat([
                    '#' + v.discipline.code + ' - ' + v.discipline.current_translation.display_name + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_disciplines[][id]',
                        value: v.discipline.id
                    }),

                    v.years + SimpleHTMLElement('input', {type: 'hidden', name: 'dr_years[]', value: v.years}),
                    v.total_hours + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_total_hours[]',
                        value: v.total_hours
                    }),

					//MOSTRA DADOS NOS ESTES CAMPOS # SALVAR DADOS//
                    v.discipline.discipline_areas[0].translations[0].display_name + SimpleHTMLElement('input', {
                        type: 'hidden',
                        name: 'dr_area[]',
                        value: v.discipline.discipline_areas[0].translations[0].display_name 
                    }),

					   SimpleHTMLElement('input', {
                        name:'dr_avaliacao[]',
                        type:'button',
                        id: 'avalicaoModal',
                        class: 'btn btn-primary ',
                        value:"+",
                        disciplina_name:v.discipline.current_translation.display_name,
                        plano_estudo:studyPlanEdition.id,
                        disciplina:v.discipline.id,
                        onClick:' modalAvalicaoAdd(this)'
                    }),
                    SimpleHTMLElement('input', {
                         name:'dr_falta[]',
                         type:'number',
                         value:falatDisc,
                         class:'form-control w-25',
                         min:0,
                         onClick:"this.removeAttribute('readonly')",
                         readonly:true,
  
                     })
                                
                ]);
                    
                dataSet.push(auxArray);
                // auxArray.push(selectAvaliaca);    

            });
            
        }























        $("#CancelarModal").click(function(){
           $('#bodyData').empty();
        });


        function modalAvalicaoAdd(ev){
            // console.log(ev)
             var id_disciplina=$(ev).attr('disciplina');
             var id_plano_estudo=$(ev).attr('plano_estudo');
             var nome_disciplina=$(ev).attr('disciplina_name');

            $("#disciplinaAvl").val(id_disciplina);
            $("#planoAvl").val(id_plano_estudo);
           

            $.ajax({
                url: "/gestao-academica/study-plan-editions/study-plan-editions_avaliacao/" + id_plano_estudo+"/"+id_disciplina,
                type: "GET",
                data:{
                    _token: '{{ csrf_token() }}'
                },
                beforeSend:function(){
                 
                },
                success:function(e){
                    let bodyData = '';
                    console.log(e);

                var compara = e['avaiacaoChecada'].map(d => d.id_avaliacao);
                    // console.log(compara);

                   if(e['avaliacaoGeral'].length>0){
                      var  checkboxAttrsAvl;
                        for (let a = 0; a < e['avaliacaoGeral'].length; a++) { 
                            bodyData += '<tr>'
                        if ($.inArray(e['avaliacaoGeral'][a].id, compara) !== -1) { 
                            $("#exameObrigatorio").css('display','block');
                            bodyData += '<td width="100"><input class="checar checar'+e['avaliacaoGeral'].length+'"   onClick="verChecagen(this)"  data-id="'+e['avaliacaoGeral'].length+'"  type="checkbox" checked name="checadas[]" value="'+e['avaliacaoGeral'][a].id+'"></td><td>'+ e['avaliacaoGeral'][a].nome+ '</td>'  
                        }
                        else{
                               bodyData += '<td width="100"><input class="checar checar'+e['avaliacaoGeral'].length+'" onClick="verChecagen(this)" type="checkbox" data-id="'+e['avaliacaoGeral'].length+'"  name="checadas[]" value="'+e['avaliacaoGeral'][a].id+'"></td><td>'+ e['avaliacaoGeral'][a].nome+ '</td>';
                             }
                            bodyData += '</tr>' 
                        }
                        
                      
                    }
                    //marcar a checkbox por defeito
                    if(e['exame_obrigatorio']==1){
                        $("#checagemExame").attr('checked',true);
                     }else{
                        $("#checagemExame").attr('checked',false);
                     }

                     if(e['avaiacaoChecada'].length>0){
                            $("#exameObrigatorio").css('display','block');
                            $("#checagemExame").val(1);
                        }else{
                            $("#checagemExame").val(0);
                            $("#exameObrigatorio").css('display','none');
                        }
                     $("#exampleModalLabel").text(nome_disciplina);
                     $('#bodyData').empty();
                     $('#bodyData').append(bodyData);
                     $("#insertMetrica").modal('show');  

                },
                 error: function(e){
                 console.log("Erro do servidor, tente novamente :"+e);   

                }

            });
        }
        
        function verChecagen(ev) {
            let checagen=$(ev).attr('data-id');
            for (var i = 0; i <  checagen.length; i++) {
                if ($(".checar").is(':checked')) {
                    $("#exameObrigatorio").css('display','block');
                    $("#checagemExame").attr('checked',true);
                    $("#checagemExame").val(1);

                }else{
                    $("#checagemExame").attr('checked',false);
                    $("#exameObrigatorio").css('display','none');
                    $("#checagemExame").val(0);

                       

                }
                
            }
              
        }
        



































        $(function () {
            loadAccessTypes();

            if (!$.isEmptyObject(selectStudyPlan)) {
                loadStudyPlanData(selectStudyPlan.val());
                
                selectStudyPlan.on('change', function () {
                    loadStudyPlanData($(this).val());
                });
                
            }

            selectYear.on('change', function () {
                var year = $(this).val();
                if (year) {
                    loadStudyPlanInputs(year);
                } else {
                    $('.spe-form').removeClass('show');
                }
            });

            selectDisciplinePrecendence.on('change', function () {
                console.log('discipline_precedence');
            });

            selectPrecendencePrecendence.on('change', function () {
                console.log('precedence_precedence');
            });

            $('#create-precedences').on('click', function () {
                loadPrecedents();
            });

            setTimeout(function () {
                $('select[name=discipline_regimes_length] :nth-child(1)').prop('selected', true);
                $('select[name=discipline_regimes_length]').change();
            }, 500);

            // # ACTION # SHOW, EDIT ##
            @if ($action !== 'create')
            $('.spe-form').addClass('show');
            let studyPlanId = "{{ $study_plan_edition->studyPlan->id }}";
            let studyPlanEditionYear = "{{ $study_plan_edition->course_year }}";
            loadStudyPlanData(studyPlanId, studyPlanEditionYear);
            @endif
        });
        /*
         * Converts an array to table row HTMLElement
         */
        function arrayToTableRowNode(row) {
            let html = '';
            html += '<tr>';
            if (Array.isArray(row) && row.length > 0) {
                row.forEach(col => {
                    html += '<td>' + col + '</td>';
                });
            }
            html += '</tr>';
            return $(html);
        }



        /**
         * jQuery serializeObject
         * @copyright 2014, macek :<paulmacek@gmail.com>
         * @link:https://github.com/macek/jquery-serialize-object
         * @license BSD
         * @version 2.5.0
         */



        (function (root, factory) {

            // AMD
            if (typeof define === 'function' && define.amd) {
                define(['exports', 'jquery'], function (exports, $) {
                    return factory(exports, $);
                });
            }

            // CommonJS
            else if (typeof exports !== 'undefined') {
                var $ = require('jquery');
                factory(exports, $);
            }

            // Browser
            else {
                factory(root, (root.jQuery || root.Zepto || root.ender || root.$));
            }

        }
        
        
        (this, function (exports, $) {

            var patterns = {
                validate: /^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
                key: /[a-z0-9_]+|(?=\[\])/gi,
                push: /^$/,
                fixed: /^\d+$/,
                named: /^[a-z0-9_]+$/i
            };

            function FormSerializer(helper, $form) {

                // private variables
                var data = {},
                    pushes = {};

                // private API
                function build(base, key, value) {
                    base[key] = value;
                    return base;
                }

                function makeObject(root, value) {

                    var keys = root.match(patterns.key), k;

                    // nest, nest, ..., nest
                    while ((k = keys.pop()) !== undefined) {
                        // foo[]
                        if (patterns.push.test(k)) {
                            var idx = incrementPush(root.replace(/\[\]$/, ''));
                            value = build([], idx, value);
                        }

                        // foo[n]
                        else if (patterns.fixed.test(k)) {
                            value = build([], k, value);
                        }

                        // foo; foo[bar]
                        else if (patterns.named.test(k)) {
                            value = build({}, k, value);
                        }
                    }

                    return value;
                }

                function incrementPush(key) {
                    if (pushes[key] === undefined) {
                        pushes[key] = 0;
                    }
                    return pushes[key]++;
                }

                function encode(pair) {
                    switch ($('[name="' + pair.name + '"]', $form).attr('type')) {
                        case 'checkbox':
                            return pair.value === 'on' ? true : pair.value;
                        default:
                            return pair.value;
                    }
                }

                function addPair(pair) {
                    if (!patterns.validate.test(pair.name)) return this;
                    var obj = makeObject(pair.name, encode(pair));
                    data = helper.extend(true, data, obj);
                    return this;
                }

                function addPairs(pairs) {
                    if (!helper.isArray(pairs)) {
                        throw new Error('formSerializer.addPairs expects an Array');
                    }
                    for (var i = 0, len = pairs.length; i < len; i++) {
                        this.addPair(pairs[i]);
                    }
                    return this;
                }

                function serialize() {
                    return data;
                }

                function serializeJSON() {
                    return JSON.stringify(serialize());
                }

                // public API
                this.addPair = addPair;
                this.addPairs = addPairs;
                this.serialize = serialize;
                this.serializeJSON = serializeJSON;
            }

            FormSerializer.patterns = patterns;

            FormSerializer.serializeObject = function serializeObject() {
                return new FormSerializer($, this).addPairs(this.serializeArray()).serialize();
            };

            FormSerializer.serializeJSON = function serializeJSON() {
                return new FormSerializer($, this).addPairs(this.serializeArray()).serializeJSON();
            };

            if (typeof $.fn !== 'undefined') {
                $.fn.serializeObject = FormSerializer.serializeObject;
                $.fn.serializeJSON = FormSerializer.serializeJSON;
            }

            exports.FormSerializer = FormSerializer;

            return FormSerializer;
        }));

        String.prototype.replaceAtOccurrence = function (searchValue, replaceValue, occurence) {
            var i = 0;
            return this.replace(searchValue, function (match) {
                i++;
                return (i === occurence) ? replaceValue : match;
            });
        };
    </script>
@endsection
