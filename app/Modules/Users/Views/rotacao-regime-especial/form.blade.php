@extends('layouts.generic_index_new', ['breadcrumb_super'=> true])

@switch($action)
@case('create') @section('title',__('Criar rotação')) @break
@case('show') @section('title',__('Ver rotação')) @break
@case('edit') @section('title',__('Editar rotação')) @break
@endswitch

@section('page-title')
@switch($action)
@case('create') @lang('Criar rotação') @break
@case('show') @lang('Ver rotação') @break
@case('edit') @lang('Editar rotação') @break
@endswitch
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="https://dev.forlearn.ao/pt">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('rotacao-regime-especial.index') }}">Rotações</a></li>

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
                    {!! Form::open(['route' => ['rotacao-regime-especial.store']]) !!}
                @break

                @case('show')
                    {!! Form::model($data) !!}
                @break

                @case('edit')
                    {!! Form::model($data, ['route' => ['rotacao-regime-especial.update', $data->id], 'method' => 'put']) !!}
                @break
            @endswitch


                <div class="row">
                    <div class="col">


                        <div class="card">
                            <div class="card-body">
                               
                                {{ Form::bsText('codigo', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                {{ Form::bsText('nome',null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show','required'], ['label' => __('translations.display_name')]) }}
                                {{ Form::bsText('descricao',null, ['placeholder' => __('Descrição'), 'disabled' => $action === 'show','required'], ['label' => __('Descrição')]) }}
                   

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
                                <a href="{{ route('rotacao-regime-especial.edit', $data->id) }}" class="btn btn-sm btn-warning mb-3">
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
