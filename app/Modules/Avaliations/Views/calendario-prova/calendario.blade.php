<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title')
    @switch($action)
        @case('create')
            CRIAR CALENDÁRIO DE PROVA
        @break

        @case('show')
            CALENDÁRIO DE PROVA
        @break

        @case('edit')
            EDITAR CALENDÁRIO DE PROVA
        @break
    @endswitch
@endsection
@isset($tb_calendario)    
    @foreach ($tb_calendario as $items)  @endforeach
@endisset
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('school-exam-calendar.index') }}">Calandário de prova</a>
    </li>
    @switch($action)
        @case('create')
            <li class="breadcrumb-item active" aria-current="page">Criar</li>
        @break

        @case('show')
            <li class="breadcrumb-item active" aria-current="page">{{ $calendario[0]->display_name }}</li>
        @break

        @case('edit')
            @isset($items)
                <li class="breadcrumb-item active" aria-current="page">{{ $items->display_name }} - {{ $items->simestre_nome }}</li>
            @endisset
        @break
    @endswitch
@endsection
@section('body')
    @if ($action === 'show')
    @endif
    @switch($action)
        @case('create')
            {!! Form::open(['route' => 'school-exam-calendar.store', 'files' => true]) !!}
        @break

        @case('show')
            {!! Form::model($calendario) !!}
        @break

        @case('edit')
            @isset($items)
                {!! Form::model($items, [
                    'route' => ['school-exam-calendar.update', $items->id_avaliacao],
                    'method' => 'put',
                    'files' => true,
                ]) !!}
            @endisset
        @break
    @endswitch
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($action == 'edit')
        @isset($items)
            <div class="card-body row">
                {{ Form::bsText('codigo', null, ['placeholder' => $items->code, 'required', 'autocomplete' => 'code'], ['label' => __('Código')]) }}
                {{ Form::bsText('nome', $items->display_name, ['value' => $items->display_name, 'placeholder' => $items->display_name, 'readonly', 'autocomplete' => 'nome'], ['label' => __('Nome da prova')]) }}
            </div>
            <div class="card-body row pt-0">
                {{ Form::bsDate('data_start', $items->date_start, ['value' => $items->date_start, 'required', 'autocomplete' => 'date_start'], ['label' => 'Data do início ']) }}
                {{ Form::bsDate('data_end', $items->data_end, ['value' => $items->data_end, 'required', 'autocomplete' => 'data_end'], ['label' => 'Data do fim ']) }}
                <input type="hidden" name="ano_lectivo" value="{{ $items->lectiveYear }}">
                <input type="hidden" name="simestre_prova" value="{{ $items->simestre }}">
            </div>
        @endisset
    @elseif ($action == 'show')
        <div class="card-body row">
            {{ Form::bsText('codigo', null, ['placeholder' => $calendario[0]->code, 'disabled' => $action === 'show', 'readonly', 'required', 'autocomplete' => 'code'], ['label' => __('Código')]) }}
            {{ Form::bsText('nome', null, ['placeholder' => $calendario[0]->display_name, 'disabled' => $action === 'show', 'required', 'autocomplete' => 'nome', 'value' => $calendario[0]->id_avaliacao], ['label' => __('Nome da prova')]) }}
        </div>
        <div class="card-body row pt-0">
            {{ Form::bsDate('data_start', $calendario[0]->date_start, ['value' => $calendario[0]->date_start, 'disabled' => $action === 'show', 'readonly', 'readonly', 'required', 'autocomplete' => 'date_start'], ['label' => 'Data do início ']) }}
            {{ Form::bsDate('data_end', $calendario[0]->data_end, ['value' => $calendario[0]->data_end, 'disabled' => $action === 'show', 'readonly', 'required', 'autocomplete' => 'data_end'], ['label' => 'Data do fim ']) }}
        </div>
    @endif


    @if ($action === 'create')
        <div class="row mb-2">
            <div class="col-6 p-2">
                <label>Selecione o periodo</label>
                <select name="simestre_prova" id="simestre_prova" class="selectpicker form-control form-control-sm"
                    style="width: 100%; !important">
                    @foreach ($periodo as $item)
                        <option value="{{ $item->id }}" selected>
                            {{ $item->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 p-2">
                <label for="">Selecione a avaliação</label>
                <select name="avalicacao" required data-live-search="true" class="selectpicker form-control">
                    @isset($Avaliacao)
                        @foreach ($Avaliacao as $item)
                            <option value="{{ $item->avaliacao_id }},{{ $item->nome }}">{{ $item->nome }}
                            </option>
                        @endforeach
                    @endisset
                    @isset($nomeAvaliacao)
                        <option value="{{ $id_Avaliacacao }},{{ $nomeAvaliacao }}">{{ $nomeAvaliacao }}</option>
                        <input type="hidden" name="ano_lectivo" value="{{ $dadosAvaliacao->anoLectivo }}">
                    @endisset
                </select>
            </div>
            <div class="col-6 p-2">
                {{-- {{ Form::bsText('codigo', null, ['placeholder' => 'Digite o código da prova', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'code'], ['label' => __('Código')]) }} --}}
                <label for="codigo">Código</label>
                <input class="form-control" placeholder="Digite o código da prova" required="" autocomplete="code"
                    name="codigo" type="text" id="codigo">
            </div>
            <div class="col-3 p-2">
                {{-- {{ Form::bsDate('data_start', null, ['placeholder' => 'Data do Início', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'date_start'], ['label' => 'Data do início ']) }} --}}
                <label for="data_start">Data do início </label>
                <input class="form-control" placeholder="Data do Início" required="" autocomplete="date_start"
                    name="data_start" type="date" id="data_start">
            </div>
            <div class="col-3 p-2">
                {{-- {{ Form::bsDate('data_end', null, ['placeholder' => 'Data do fim ', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'data_end'], ['label' => 'Data do fim ']) }} --}}
                <label for="data_end">Data do fim </label>
                <input class="form-control" placeholder="Data do fim " required="" autocomplete="data_end"
                    name="data_end" type="date" id="data_end">
            </div>
        </div>
    @endif

    @switch($action)
        @case('create')
            <div id="nextBtn">
                @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
                    <button type="submit" class="btn btn-lg btn-success mb-3 float-right">
                        @icon('fas fa-plus-circle')
                        Gravar
                    </button>
                @endif
            </div>
        @break

        @case('edit')
            @isset($items)
                <div id="nextBtn">
                    @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
                        <button type="submit" class="btn btn-success p-2 mb-1 mr-3 float-right" id="editUser">
                            @icon('fas fa-edit')
                            @lang('common.edit')
                        </button>
                    @endif
                </div>
            @endisset
        @break
    @endswitch
    {!! Form::close() !!}
@endsection
