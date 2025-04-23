@section('title', __('Orçamentos'))
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        a: {}

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }
    </style>


  

 

    <div class="content-panel" style="padding:0">
        @include('GA::budget.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('budget.index') }}">Orçamentos</a></li>

                                <li class="breadcrumb-item active" aria-current="page">
                                    @switch($action)
                                        @case('create')
                                            Criar orçamentos
                                        @break

                                        @case('show')
                                            Ver orçamentos
                                        @break

                                        @case('edit')
                                            Editar orçamentos
                                        @break
                                    @endswitch
                                </li>

                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1> @switch($action)
                                @case('create')
                                    Criar orçamentos
                                @break

                                @case('show')
                                    Ver orçamentos
                                @break

                                @case('edit')
                                    Editar orçamentos
                                @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-12">

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['budget.store']]) !!}
                    @break

                    @case('show')
                        {!! Form::open(['route' => ['budget.update', $budget->id]]) !!}
                    @break

                    @case('edit')
                        {!! Form::open(['route' => ['budget.update', $budget->id]]) !!}
                    @break
                @endswitch







                <div class="col-12">
                    <div class="row">
                        <div class="form-group col-12">


                            {{-- Formulários para orçamentos --}}
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-6">
                                        {{ Form::bsCustom('name', $budget->name ?? null, ['type' => 'text', 'placeholder' => '', 'disabled' => $action === 'show', 'required' => true], ['label' => 'Nome']) }}
                                    </div>
                                    <div class="form-group col-6">
                                        <div class="col-12">
                                            <div class="form-group col">
                                                <label for="budget_type">Tipo de orçamento</label>
                                                <select class="selectpicker form-control form-control-sm" name="budget_type" @if ($action=="show")
                                                disabled
                                                @endif id="budget_type" data-actions-box="true" data-live-search="true"
                                                    required>
                                                    <option></option>




                                                    @if (isset($budget->budget_type))
                                                        @foreach ($types as $item)
                                                            @if ($budget->budget_type == $item->id)
                                                                <option value="{{ $item->id }}" selected>
                                                                    {{ $item->name }}</option>
                                                            @else
                                                                <option value="{{ $item->id }}">
                                                                    {{ $item->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @foreach ($types as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->name }}</option>
                                                        @endforeach
                                                    @endif


                                                </select>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-8">
                                        <label for="description" class="ml-3">Descrição</label>
                                        <textarea name="description" class="form-control ml-3" id="description" cols="30" rows="10"  @if ($action=="show")
                                        disabled @endif required>{{ $budget->description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group  col">

                                        @switch($action)
                                            @case('create')
                                                <button type="submit" class="create-event btn ml-3 btn-success mb-3 ml-3">
                                                    @icon('fas fa-plus-circle')
                                                    @lang('common.create')
                                                </button>
                                            @break

                                            @case('show')
                                                <a href="{{ route('budget.edit', $budget->id) }}"
                                                    class="create-event btn ml-3 btn-warning mb-3 ml-3">
                                                    @icon('fas fa-edit')
                                                    @lang('common.edit')
                                                </a>
                                            @break

                                            @case('edit')
                                                <button type="submit" class="create-event btn ml-3 btn-success mb-3 ml-3">
                                                    @icon('fas fa-save')
                                                    @lang('common.save')
                                                </button>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>



                {!! Form::close() !!}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script></script>

@endsection
