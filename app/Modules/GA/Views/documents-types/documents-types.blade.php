@extends('layouts.generic_index_new', ['breadcrumb_super'=> true])

@switch($action)
@case('create') @section('title',__('Criar Tipos de Documentos')) @break
@case('show') @section('title',__('Ver Tipos de Documentos')) @break
@case('edit') @section('title',__('Editar Tipos de Documentos')) @break
@endswitch

@section('page-title')
@switch($action)
@case('create') @lang('Criar Tipos de Documentos') @break
@case('show') @lang('Ver Tipos de Documentos') @break
@case('edit') @lang('Editar Tipos de Documentos') @break
@endswitch
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('documents-types.index') }}">Tipos de Documentos</a></li>
                                
                                <li class="breadcrumb-item active" aria-current="page">
                                    @switch($action)
                                        @case('create')
                                           Criar
                                        @break

                                        @case('show')
                                            Ver
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
    {!! Form::open(['route' => ['documents-types.store']]) !!}
@break

@case('show')
    {!! Form::model($data) !!}
@break

@case('edit')
    {!! Form::model($data, ['route' => ['documents-types.update', $data->id], 'method' => 'put']) !!}
@break
@endswitch


<div class="col">
                <div class="row">

                    
                        <div class="card">
                            <div class="card-body">
                               
                                {{ Form::bsText('created_by', null, ['placeholder' => __('created_by') ?? "", 'disabled' => $action === 'show', 'disabled' => $action === 'create', 'required'], ['label' => __('Criado por')]) }}
                                {{ Form::bsText('name',null, ['placeholder' => __('name'), 'disabled' => $action === 'show','required'], ['label' => __('name')]) }}
                                {{ Form::bsText('observation',null, ['placeholder' => __('observation'), 'disabled' => $action === 'show','required'], ['label' => __('Observação')]) }}
                                

                                @switch($action)
                                @case('create')
                                <button type="submit" style="margin-left: 15px"  class="btn btn-sm btn-success mb-3 ">
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
                                <a href="{{ route('documents-types.edit', $data->id) }}" class="btn btn-sm btn-warning mb-3">
                                    @icon('fas fa-edit')
                                    @lang('common.edit')
                                </a>
                                @break
                            @endswitch
    
                            
                            </div>
                        </div>

                       
                    </div>
                </div>

             
                {!! Form::close() !!}

          @endsection