@extends('layouts.generic_index_new')
@switch($action)
    @case('create') @section('title',__('Payments::articles.create_article')) @break
    @case('show') @section('title',__('Payments::articles.article')) @break
    @case('edit') @section('title',__('Payments::articles.edit_article')) @break
@endswitch
@section('page-title')
    @switch($action)
        @case('create')
            @section('title', __('Payments::articles.create_article'))
        @break

        @case('show')
            @section('title', __('Payments::articles.article'))
        @break

        @case('edit')
            @section('title', __('Payments::articles.edit_article'))
        @break
    @endswitch
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('articles.index') }}" class="">
            Emolumentos - Propinas
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        @switch($action)
            @case('create')
                Criar
            @break

            @case('show')
                Visualizar
            @break

            @case('edit')
                Editar
            @break
        @endswitch
    </li>
@endsection
@section('body')
    @switch($action)
        @case('create')
            {!! Form::open(['route' => ['articles.store']]) !!}
        @break

        @case('show')
            {!! Form::model($article) !!}
        @break

        @case('edit')
            {!! Form::model($article, ['route' => ['articles.update', $article->id], 'method' => 'put']) !!}
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
        
            <div style="" >
            @switch($action)
                @case('create')
                    <button type="submit" class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>
                        @lang('common.create')
                    </button>
                @break

                @case('edit')
                    <button type="submit" class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-save"></i>
                        @lang('common.save')
                    </button>
                @break

                @case('show')
                    @php
                        $array = $idAno_lectivo . ',' . $article->id;
                    @endphp
                    <a href="{{ route('articles.edit', $array) }}" class="btn btn-sm btn-warning mb-3">
                        <i class="fas fa-edit"></i>
                        @lang('common.edit')
                    </a>
                @break  
            @endswitch
            </div>  
            
         <div class="card">
                <div class="row">
                    <input type="hidden" name="idAno_lectivo" value="{{ $idAno_lectivo }}">
                    <div class="col-4">
                        {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                    </div>
                    <div class="col-4">
                        {{ Form::bsText('base_value', null, ['placeholder' => 'AKZ', 'disabled' => $action === 'show', 'required', 'min' => 0, 'max' => 500000], ['label' => __('Payments::articles.base_value')]) }}
                    </div>
                    <!-- Formulário escondido inicialmente -->
                    <div id="Myform" class="col-4" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#f2f4f7; padding:30px; border-radius:10px; z-index:1000; box-shadow:0 0 15px rgba(0,0,0,0.3); width:400px; align-items: center;">
                    <label for="tiposD">Tipos de Documentos</label>
                        <select class="selectpicker form-control form-control-sm" name="documentation_type_id" id="documentation_type_id" data-actions-box="true" data-live-search="true" @if($action == 'show') disabled @endif>
                            <option value="" disabled  @if($action == 'create') selected @endif>
                                    Tipos de Documentos
                            </option>
                            @if(isset($tiposdocumentos))
                                @foreach ($tiposdocumentos  as $tiposdocumento)
                                    <option value="{{ $tiposdocumento->id }}"
                                        @if(($action == 'edit' || $action == 'show') && $article->id_category == $tiposdocumento->id) selected @endif>
                                            {{ $tiposdocumento->observation }}
                                    </option>
                                @endforeach
                            @endif
                        </select> 
                            <div style="text-align: center; margin-top: 20px;">
                                <button type="button" class="btn btn-success" id="prosseguir">Prosseguir</button>
                                </div>
                            </div>                 
                    <!-- Formulário escondido inicialmente -->
                    <div class="col-4">
                        {{ Form::bsCheckbox('active', 1, null, ['disabled' => $action === 'show'], ['label' => __('Tipos de Documentos')]) }}
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="fase_type">Categoria</label>
                            <select class="selectpicker form-control form-control-sm" name="categoria" id="fase_type"
                                data-actions-box="true" data-live-search="true">
                                <option value="" disabled {{ $action === 'create' ? 'selected' : '' }}>
                                    Selecione a categoria
                                </option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @if ($action === 'edit' && $article->id_category == $categoria->id) selected @endif>
                                        {{ $categoria->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3"> @lang('Payments::articles.extra_fees.extra_fee')</h5>
                            @if ($action === 'edit' || $action === 'create')
                                <button data-toggle='modal' type='button' data-type='add' data-target='#modal_type_7'
                                    class='btn btn-sm btn-success mb-3'>
                                    <i class='fas fa-plus'></i>
                                </button>
                            @endif
                            <div id="extra_fees"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3"> @lang('Payments::articles.monthly_charge.monthly_charge')</h5>
                            @if ($action === 'edit' || $action === 'create')
                                <button data-toggle='modal' type='button' data-type='add' data-target='#modal_type_8'
                                    class='btn btn-sm btn-success mb-3'>
                                    <i class='fas fa-plus'></i>
                                </button>
                            @endif
                            <div id="monthly_charge"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"> @lang('Payments::articles.monthly_charge.monthly_charge')</h5>
                    @if ($action === 'edit' || $action === 'create')
                        <button data-toggle='modal' type='button' data-type='add' data-target='#modal_type_8'
                            class='btn btn-sm btn-success mb-3'>
                            <i class='fas fa-plus'></i>
                        </button>
                    @endif
                    <div id="monthly_charge"></div>
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
                                        href="#language{{ $language->id }}" data-toggle="tab">{{ $language->name }}</a>
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
                                {{ Form::bsText('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show'], ['label' => __('translations.description')]) }}
                                {{ Form::bsText('acronym[' . $language->id . ']', $action === 'create' ? old('acronym.' . $language->id) : $translations[$language->id]['acronym'] ?? null, ['placeholder' => __('Sigla'), 'disabled' => $action === 'show'], ['label' => __('Sigla')]) }}
                                {{ Form::bsText('observation[' . $language->id . ']', $action === 'create' ? old('observation.' . $language->id) : $translations[$language->id]['observation'] ?? null, ['placeholder' => __('Observação'), 'disabled' => $action === 'show'], ['label' => __('Observação')]) }}
                            </div>
                            {{-- CheckBox --}}
                            @if ($action === 'edit' || $action === 'show')
                                <div class="col " style="width: 260px">
                                    <div class="row">
                                        <div class="col custom-control custom-radio custom-control-inline m-0 16px p-0">
                                            @if ($action === 'show')
                                                @if ($article->code_reference_discipline != null)
                                                    <input hidden checked disabled type="checkbox" id="customRadioInline1"
                                                        class="custom-control-input">
                                                    <label hidden style="font-size: 1pc" class="custom-control-label"
                                                        for="customRadioInline1">Associar com disciplinas</label>
                                                @else
                                                    <input hidden disabled type="checkbox" id="customRadioInline1"
                                                        class="custom-control-input">
                                                    <label hidden style="font-size: 1pc" class="custom-control-label"
                                                        for="customRadioInline1">Associar com disciplinas</label>
                                                @endif
                                            @else
                                                @if ($article->code_reference_discipline != null)
                                                    <input hidden checked type="checkbox" id="customRadioInline1"
                                                        name="emolument_disciplina" class="custom-control-input"
                                                        value="iExa">
                                                    <label hidden style="font-size: 1pc" class="custom-control-label"
                                                        for="customRadioInline1">Associar com disciplinas</label>
                                                @else
                                                    <input hidden type="checkbox" id="customRadioInline1"
                                                        name="emolument_disciplina" class="custom-control-input"
                                                        value="iExa">
                                                    <label hidden style="font-size: 1pc" class="custom-control-label"
                                                        for="customRadioInline1">Associar com disciplinas</label>
                                                @endif
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endif


                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
@section('models')
    @include('layouts.backoffice.modals.modal_type_7')
    @include('layouts.backoffice.modals.modal_type_8')
@endsection
@section('scripts')
    @parent
    <script>
        const initialDataExtraFees = [];

        @if ($action === 'edit' || $action === 'show')

            const dataExtraFees = JSON.parse('{!! json_encode($article->extra_fees) !!}');

            $.each(dataExtraFees, function(k, v) {
                initialDataExtraFees.push([{
                    text: v.fee_percent,
                    value: v.fee_percent,
                    name: 'extra_fees_percent[]'
                }, {
                    text: v.max_delay_days,
                    value: v.max_delay_days,
                    name: 'extra_fees_delay[]'
                }]);
            });
        @endif

        const dt7 = new DynamicDatatable('#extra_fees', 'table_extra_fees', [{
            text: '{!! trans('Payments::articles.extra_fees.percent') !!}',
            name: 'extra_fees_percent[]'
        }, {
            text: '{{ trans('Payments::articles.extra_fees.delay') }}',
            name: 'extra_fees_delay[]'
        }], initialDataExtraFees, '{!! trans('common.delete') !!}', 'modal_type_7', '{!! $action !!}');
        dt7.initialize();

        const initialDataMonthlyCharge = [];

        @if ($action === 'edit' || $action === 'show')

            const dataMonthlyCharges = JSON.parse('{!! json_encode($article->monthly_charges) !!}');
            const months = '{!! trans('Payments::articles.monthly_charge.months') !!}'.split('_');

            console.log(dataMonthlyCharges);
            console.log(months);

            $.each(dataMonthlyCharges, function(k, v) {
                initialDataMonthlyCharge.push([{
                    text: v.course.current_translation.display_name,
                    value: v.course.id,
                    name: 'monthly_charge_course[]'
                }, {
                    text: v.course_year,
                    value: v.course_year,
                    name: 'monthly_charge_course_year[]'
                }, {
                    text: months[v.start_month - 1],
                    value: v.start_month,
                    name: 'monthly_charge_start_month[]'
                }, {
                    text: months[v.end_month - 1],
                    value: v.end_month,
                    name: 'monthly_charge_end_month[]'
                }, {
                    text: v.charge_day,
                    value: v.charge_day,
                    name: 'monthly_charge_charge_day[]'
                }]);
            });
        @endif

        const dt8 = new DynamicDatatable('#monthly_charge', 'table_monthly_charge', [{
            text: '{!! trans('Payments::articles.monthly_charge.course') !!}',
            name: 'monthly_charge_course[]'
        }, {
            text: '{{ trans('Payments::articles.monthly_charge.course_year') }}',
            name: 'monthly_charge_course_year[]'
        }, {
            text: '{{ trans('Payments::articles.monthly_charge.start_month') }}',
            name: 'monthly_charge_start_month[]'
        }, {
            text: '{{ trans('Payments::articles.monthly_charge.end_month') }}',
            name: 'monthly_charge_end_month[]'
        }, {
            text: '{{ trans('Payments::articles.monthly_charge.charge_day') }}',
            name: 'monthly_charge_charge_day[]'
        }], initialDataMonthlyCharge, '{!! trans('common.delete') !!}', 'modal_type_8', '{!! $action !!}');
        dt8.initialize();


    document.getElementById("active").addEventListener("change", function() {
    const formContainer = document.getElementById("Myform");
    const botaoProsseguir = document.getElementById("prosseguir").addEventListener("click", function() {
        formContainer.style.display = "none";
    });

    if (this.checked) {
 
        formContainer.style.display = "block";

    } else {
 
        formContainer.style.display = "none";
    }
});

    </script>
@endsection
