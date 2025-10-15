@switch($action)
    @case('create') @section('title',__('Payments::banks.create_bank')) @break
@case('show') @section('title',__('Payments::banks.bank')) @break
@case('edit') @section('title',__('Payments::banks.edit_bank')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

    <div class="content-panel" style="padding: 0px;">
        @include('RH::index_menu')
        {{-- @include('Payments::requests.navbar.navbar') --}}       


        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            Configurações RH
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('banks.create') }} @break
                            @case('show') {{ Breadcrumbs::render('banks.show', $bank) }} @break
                            @case('edit') {{ Breadcrumbs::render('banks.edit', $bank) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
            
        
        <!-- Modal editar banco -->
        <div class="modal fade" id="editarbanco" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered rounded" role="document">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Editar banco</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form method="POST" action="{{route('banco.editar')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="id_bank" id="id_bank">
                            <label for="exampleInputEmail1">Código</label>
                            <input type="text" name="code" class="form-control" id="code"  >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Nome</label>
                            <input type="text" name="nome" class="form-control" id="nome">
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn rounded btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn rounded btn-primary">Editar</button>
                    </div>
                </form>
    
            </div>
            </div>
        </div>   
            
        <p class="btn-menu col-md-0 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                @include('RH::index_menuConfiguracoes')
                <div style="background-color: #f8f9fa" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div  class="criarCodigo ">
                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 col-12 ">
                            <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3 "></div>
                           
                           
                            <div class="col-md-12 align-items-end ">
                                <div class="float-right  d-flex flex-row-reverse bd-highlight">
                                    <div class="p-2 bd-highlight"><h5 class="text-muted text-uppercase"> criar Bancos</h5></div>                                    
                                </div>
                            </div>

                            {{-- Main content --}}
                            <div class="content">
                                <div class="container-fluid">

                                    @switch($action)
                                        @case('create')
                                        {!! Form::open(['route' => ['recurso-humano.store-banco']]) !!}
                                        @break
                                        @case('show')
                                        {!! Form::model($bank) !!}
                                        @break
                                        @case('edit')
                                        {!! Form::model($bank, ['route' => ['banks.update', $bank->id], 'method' => 'put']) !!}
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
                                                <a href="{{ route('banks.edit', $bank->id) }}"
                                                    class="btn btn-sm btn-warning mb-3">
                                                    <i class="fas fa-edit"></i>
                                                    @lang('common.edit')
                                                </a>
                                                @break
                                            @endswitch

                                            <div class="card">
                                                <div class="row">
                                                    <div class="col-4">
                                                        {{ Form::bsText('code', null, ['placeholder' => __('common.code'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.code')]) }}
                                                    </div>
                                                    <div class="col-8">
                                                        {{ Form::bsText('display_name', null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', 'required'], ['label' => __('translations.display_name')]) }}
                                                    </div>
                                                    {{-- <div class="col-6">
                                                        {{ Form::bsText('account_number', null, ['placeholder' => __('Payments::banks.account_number'), 'disabled' => $action === 'show', 'required'], ['label' => __('Payments::banks.account_number')]) }}
                                                    </div>
                                                    <div class="col-6">
                                                        {{ Form::bsText('iban', null, ['placeholder' => 'IBAN', 'disabled' => $action === 'show', 'required'], ['label' => 'IBAN']) }}
                                                    </div> --}}
                                                    <div class="col-6" hidden>
                                                        {{ Form::bsText('type_conta_entidade', 'rh', ['placeholder' => 'Entidade', 'disabled' => $action === 'show', 'required'], ['label' => 'Entidade']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
                                </div>
                            </div>                                                   

                        </div>

                        <div class="card">
                            <div class="card-body">

                                <table id="banks-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                    </thead>
                                </table>

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
    <script>

        $(function () {
            $('#banks-table').DataTable({
                ajax: '{!! route('recurso-humano.ajax-banco') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [
                    {
                        data: 'code',
                        name: 'code',
                        visible: false
                    }, {
                        data: 'display_name',
                        name: 'display_name'
                    }, 
                    // {
                    //     data: 'account_number',
                    //     name: 'account_number'
                    // }, {
                    //     data: 'iban',
                    //     name: 'iban'
                    // }, 
                    {
                        data: 'created_by',
                        name: 'u1.name',
                        visible: false
                    }, {
                        data: 'updated_by',
                        name: 'u2.name',
                        visible: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection