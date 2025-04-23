@switch($action)
    @case('create')
        @section('title', __('GA::study-plans.create_study_plan'))
    @break

    @case('show')
        @section('title', __('GA::study-plans.study_plan'))
    @break

    @case('edit')
        @section('title', __('GA::study-plans.edit_study_plan'))
    @break
@endswitch
  
@extends('layouts.backoffice')

@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include('GA::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">


                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create')
                                    @lang('GA::study-plans.create_study_plan')
                                @break

                                @case('show')
                                    @lang('GA::study-plans.study_plan')
                                @break

                                @case('edit')
                                    @lang('GA::study-plans.edit_study_plan')
                                @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create')
                                {{ Breadcrumbs::render('study-plans.create') }}
                            @break

                            @case('show')
                                {{ Breadcrumbs::render('study-plans.show', $study_plan) }}
                            @break

                            @case('edit')
                                {{ Breadcrumbs::render('study-plans.edit', $study_plan) }}
                            @break
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
                        {!! Form::open(['route' => ['study-plans.store']]) !!}
                    @break

                    @case('show')
                        {!! Form::model($study_plan) !!}
                    @break

                    @case('edit')
                        {!! Form::model($study_plan, ['route' => ['study-plans.update', $study_plan->id], 'method' => 'put']) !!}
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
                                <a href="{{ route('study-plans.edit', $study_plan->id) }}" class="btn btn-sm btn-warning mb-3">
                                    @icon('fas fa-edit')
                                    @lang('common.edit')
                                </a>
                                
                                <a href="{{ route('study-plans.pdf', $study_plan->id) }}" class="btn btn-sm btn-primary mb-3 mr-2">
                                    @icon('fas fa-file-pdf')
                                     Imprimir 
                                </a> 
                                                          
                            @break
                          
                        @endswitch


                       

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                </div>
                                <div class="col-6">
                                    @include('GA::study-plans.partials.course')
                                </div>
                            </div>
                            <hr>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">@lang('GA::study-plans.discipline_regimes') <span id="duped-disciplines"
                                            style="color: red" hidden> - Disciplina(s) duplicadas!</span></h5>
                                    @if ($action === 'edit' || $action === 'create')
                                        <button data-toggle='modal' type='button' data-type='add'
                                            data-target='#modal_type_2' class='btn btn-sm btn-success mb-3'>
                                            @icon('fas fa-plus')
                                        </button>
                                    @endif
                                    <div id="study_plan_has_regime">

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
                                    @if (isset($languages) && count($languages) > 0)
                                        @foreach ($languages as $language)
                                            <li class="nav-item">
                                                <a class="nav-link @if ($language->default) active show @endif"
                                                    href="#language{{ $language->id }}"
                                                    data-toggle="tab">{{ $language->name }}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    @foreach ($languages as $language)
                                        <div class="tab-pane row @if ($language->default) active show @endif"
                                            id="language{{ $language->id }}">
                                            {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                            {{ Form::bsText('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                            {{ Form::bsText('abbreviation[' . $language->id . ']', $action === 'create' ? old('abbreviation.' . $language->id) : $translations[$language->id]['abbreviation'] ?? null, ['placeholder' => __('translations.abbreviation'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.abbreviation')]) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
                 {{-- Formulário para gerar o pdf --}}
                <div>



                
                    <br>
                <br>
                <br>
                <br>
                <br>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.backoffice.modals.modal_type_1')
    @include('layouts.backoffice.modals.modal_type_2')
    @isset($study_plan->id)
        @include('GA::study-plans.model.study')
    @endisset

@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/backoffice/dropdown-extra-fields.js') }}"></script>
    <script>
        var discipline_regimes = {!! json_encode($discipline_regimes) !!};
        var rowCount = 0;

        $(function() {
            //When modal add Study plan opens will initialize the dropdown-extra-fields control
            $(document).on("show.bs.modal", "#modal_type_2", function() {

                var preparedData = [];

                //initial data
                $.each(discipline_regimes, function(k, v) {
                    var auxData = {

                        checkbox: {
                            attributes: [{
                                key: 'value',
                                value: v.id
                            }, {
                                key: 'text',
                                value: v.current_translation.description
                            }, {
                                key: 'name',
                                value: 'dr_discipline_regimes[]'
                            }],
                            label: v.current_translation.description
                        },
                        input: {
                            attributes: [{
                                key: 'type',
                                value: 'number',
                            }, {
                                key: 'name',
                                value: 'dr_discipline_regimes_hours[]'
                            }, {
                                key: 'required',
                                value: 'required'
                            }, {
                                key: 'min',
                                value: '0'
                            }]
                        }
                    };
                    //add options to dropdown
                    preparedData.push(auxData);

                });

                //create dropdown extra object
                var data = {
                    text: 'selecione um regime',
                    options: preparedData
                };
                $("#dd-extra").DropdownExtra(data).on("changed", function(e, isOpen) {
                    // in case if there is needed a trigger for when is open or close
                });
            });
        });


        /* STUDY PLAN DISCIPLINES - REGIMES */
        var initialData = [];

        @if ($action === 'edit' || $action === 'show')

            // create dynamic list with initial data
            @php($disciplines = $study_plan->study_plans_has_disciplines)
            var data2 =
                {!! json_encode($disciplines) !!}

            var disciplineCount = {};

            $.each(data2, function(k, v) {
                var thisRowCount = ++rowCount;
                if (!disciplineCount[v.discipline.id]) {
                    disciplineCount[v.discipline.id] = 1;
                } else {
                    disciplineCount[v.discipline.id] += 1;
                }

                var auxArray = [{
                    text: thisRowCount,
                    value: thisRowCount,
                    name: "rowNumber[]"
                }, {
                    text: '#' + v.discipline.code + " - " + v.discipline.current_translation.description,
                    value: v.discipline.id,
                    name: "dr_disciplines[]"
                }, {
                    text: v.discipline_period.current_translation.description,
                    value: v.discipline_period.id,
                    name: "dr_discipline_periods[]"
                }, {
                    text: v.years,
                    value: v.years,
                    name: "dr_years[]"
                }, {
                    text: v.total_hours,
                    value: v.total_hours,
                    name: "dr_total_hours[]"
                }];

                auxInnerArray = [];
                $.each(v.study_plans_has_discipline_regimes, function(j, dr) {

                    auxInnerArray.push({
                        text: dr.discipline_regime.current_translation.display_name,
                        value: dr.discipline_regime.id,
                        name: "dr_discipline_regimes[]"
                    });
                });
                auxArray.push(auxInnerArray);

                auxInnerArray = [];
                $.each(v.study_plans_has_discipline_regimes, function(j, dr) {
                    auxInnerArray.push({
                        text: dr.hours,
                        value: dr.hours,
                        name: "dr_discipline_regimes_hours[]"
                    });
                });

                auxArray.push(auxInnerArray);

                initialData.push(auxArray);
            });


            var disciplineDupes = false;
            $.each(disciplineCount, function(i, v) {
                if (v > 1) {
                    disciplineDupes = true;
                }
            });

            if (disciplineDupes) {
                $('#duped-disciplines').attr('hidden', false);
            }
        @endif

        //creates dynamic datatable object
        var dt2 = new DynamicDatatable("#study_plan_has_regime", "table_study_plan_has_regime", [{
            text: "#",
            name: "rowNumber[]"
        }, {
            text: "Disciplinas",
            name: "dr_disciplines[]"
        }, {
            text: "Período",
            name: "dr_discipline_periods[]"
        }, {
            text: "Ano",
            name: "dr_years[]"
        }, {
            text: "Carga horária",
            name: "dr_total_hours[]"
        }, {
            text: "Regimes",
            name: "dr_discipline_regime[]"
        }, {
            text: "Horas",
            name: "dr_discipline_regime_hours[]"
        }], initialData, "Eliminar", "modal_type_2", "{!! $action !!}");

        dt2.initialize();
        
     @isset($study_plan->id)    
        const btnDanges = $('#table_study_plan_has_regime .btn-danger');
        btnDanges.each((index, item) => {
            item.classList.remove('btn-danger');
            item.classList.add('btn-warning');
            let icon = item.querySelector('i');
            icon.classList.remove('fa-trash-alt');
            icon.classList.add('fa-h');
        })
        
        btnDanges.on('click',(e)=>{
            const row = getRow(e);
            const children = row.querySelectorAll('td');
            let obj = {
                discipline_id: children[1] ? getInputValue(children[1]) : "",
                periodo_id: children[2] ? getInputValue(children[2]) : "",
                ano: children[3] ? getInputValue(children[3]) : "",
                carga_horario: children[4] ? getInputValue(children[4]) : "",
                regimes: children[5] ? getInputList(children[5]) : [],
                horas: children[6] ? getInputList(children[6]) : [],
            }
            modalCargaHorario(obj);  
        })

        function getRow(e){
            let row = e.target.parentElement.parentElement;
            let tag = row.nodeName
            if(tag == "TR") return row;
            return row.parentElement;
        }

        function getInputValue(children){
            const input = children.querySelector('input');
            return input.value;
        }

        function getInputList(children){
            const items = children.querySelectorAll('li');
            let array = [];
            items.forEach(element => {
                array.push({
                    value : element.querySelector('input').value,
                    label: element.querySelector('span').innerHTML.trim(),
                });
            });
            return array;
        }

        function modalCargaHorario(obj){
            const model_id = "#modal-carga-horario";
            
            const modelCargaHorario = $(model_id);
            const regimes = document.querySelector(model_id+' #regimes');

            const cargaHorarioInput = document.querySelector(model_id+' #carga_horario_input');
            const disciplineInput = document.querySelector(model_id+' #discipline_input');
            const periodoInput = document.querySelector(model_id+' #periodo_input');
            const anoInput = document.querySelector(model_id+' #ano_input');
            const horaTotal = document.querySelector(model_id+' #hora_total');

            let html = '';
            let tam = obj.regimes.length;
            for(let i = 0; i < tam; i++){
                html += `<div class="d-flex" ${i != 0 ? 'mt-2' : ''}>
                            <div class="w-100">${obj.regimes[i].label}</div>
                            <div class="flex-shrink-1">
                                <input class="form-control rounded w-50 m-1 carga-item" type="number" name="horas[]" value="${obj.horas[i].value}" onChange="changValueRegimes()"/>
                                <input class="form-control rounded w-50 m-1" type="hidden" name="regimes[]" value="${obj.regimes[i].value}"/>
                            </div>
                         </div>`
            }
            regimes.innerHTML = html;
            
            cargaHorarioInput.value = obj.carga_horario;
            disciplineInput.value = obj.discipline_id;
            periodoInput.value = obj.periodo_id;
            anoInput.value = obj.ano;
            
            horaTotal.value = obj.carga_horario;
            //horaTotal.setAttribute('min',obj.carga_horario);
            
            modelCargaHorario.modal('show');
        }

        function changValueRegimes(){
            let soma = 0;
            const cargaItems = document.querySelectorAll('.carga-item');
    
            const cargaHorarioInput = document.querySelector('#carga_horario_input');
            const horaTotal = document.querySelector('#modal-carga-horario #hora_total');

            cargaItems.forEach( item => soma += parseInt(item.value) );
            
            horaTotal.value = soma;
            //horaTotal.setAttribute('min',soma);
        }
        
    @endisset        
        
    </script>
@endsection
