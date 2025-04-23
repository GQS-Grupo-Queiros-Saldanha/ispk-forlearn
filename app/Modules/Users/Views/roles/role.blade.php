@section('title',__('RH - criar cargo'))
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

<!-- Modal  que apresenta a loande do  site -->
<div style="z-index: 1900" class="modal fade modal_loader" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
</div>



<div class="content-panel">
    @include('RH::index_menu')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>CONFIGURAÇÕES RH</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>



    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuConfiguracoes')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col-md-10"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>

                        <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">
                            @switch($action)
                                @case('create') @lang('Users::roles.create_role') @break
                                @case('show') @lang('Users::roles.role') @break
                                @case('edit') @lang('Users::roles.edit_role') @break
                            @endswitch
                        </h5>


                        {{-- <div class="content"> --}}
                        <div class="container-fluid">
                                
                            @switch($action)
                                @case('create')
                                {!! Form::open(['route' => ['roles.store']]) !!}
                                @break
                                @case('show')
                                {!! Form::model($role) !!}
                                @break
                                @case('edit')
                                {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'put']) !!}
                                @break
                            @endswitch
                            
                            
                            <div class="form-row">
                                <div class="col" style="background-color: #f5fcff">
            
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
            
                                    <div class="card">
                                        <div class="card-body" style="background-color: #f5fcff">
                                            {{ Form::bsText('name', null, ['placeholder' => __('Users::roles.name'), 'disabled' => $action === 'show', 'required'], ['label' => __('Users::roles.name')]) }}
                                        </div>
                                    </div>
            
                                </div>
                            </div>
                            

                            <!-- Translations -->
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="card" style="background-color: #f5fcff">
                                        
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
                                                    <div class="tab-pane @if($language->default) active show @endif" id="language{{ $language->id }}">
                                                        {{ Form::bsText('display_name['.$language->id.']', $action === 'create' ? old('display_name.'.$language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                                        {{ Form::bsText('description['.$language->id.']', $action === 'create' ? old('description.'.$language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            

                            <div class="form-row">
                                <div class="col">
                                    
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
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning mb-3">
                                            @icon('fas fa-edit')
                                            @lang('common.edit')
                                        </a>
                                        @break
                                    @endswitch

                                </div>
                            </div>
            
                            {!! Form::close() !!}
            
                        </div>                       
                        

                    </div>
                </div>
            </div>
        </div>
    </div>




    
   
</div>

@endsection
@section('scripts')

@parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

@endsection