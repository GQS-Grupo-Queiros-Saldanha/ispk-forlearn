@extends('layouts.generic_index_new', ['breadcrumb_super'=> true])

@switch($action)
@case('create') @section('title',__('Criar grau académico')) @break
@case('show') @section('title',__('Ver grau académico')) @break
@case('edit') @section('title',__('Editar grau académico')) @break
@endswitch

@section('page-title')
@switch($action)
@case('create') @lang('Criar grau académico') @break
@case('show') @lang('Ver grau académico') @break
@case('edit') @lang('Editar grau académico') @break
@endswitch
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="https://dev.forlearn.ao/pt">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('grau-academico.index') }}">Grau académico</a></li>

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
        @case('historico')
            Histórico
        @break
    @endswitch
</li>
@endsection

@section('body')
                @switch($action)
                @case('create')
                    {!! Form::open(['route' => ['grau-academico.store']]) !!}
                @break

                @case('show')
                    {!! Form::model($data) !!}
                @break

                @case('edit')
                    {!! Form::model($data, ['route' => ['grau-academico.update', $data->id], 'method' => 'put']) !!}
                @break
            @endswitch


                <div class="row">
                    <div class="col">


                        <div class="card">
                            <div class="card-body">
                               
                                {{ Form::bsText('codigo', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                {{ Form::bsText('nome',null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show','required'], ['label' => __('translations.display_name')]) }}
                                {{ Form::bsText('descricao',null, ['placeholder' => __('Descrição'), 'disabled' => $action === 'show','required'], ['label' => __('Descrição')]) }}
                                {{Form::bsText('abreviacao',null, ['placeholder' => __('Abreviação'), 'disabled' => $action === 'show','required'], ['label' => __('Abreviação')]) }}
                                

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
                                <a href="{{ route('grau-academico.edit', $data->id) }}" class="btn btn-sm btn-warning mb-3">
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
