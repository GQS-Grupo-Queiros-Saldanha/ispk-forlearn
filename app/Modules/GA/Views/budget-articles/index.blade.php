@section('title', __('Capítulos de orçamentos'))
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
    <!-- Modal -->
    <div class="modal fade" id="Modalconfirmar" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="Modalconfirmar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 10px;">

                <div class="modal-body">
                    <center><i class="fas fa-trash-alt btn-danger"
                            style="font-size: 30px;padding: 30px;border-radius: 60px;color:white;" aria-hidden="true"></i>
                    </center>
                    <p style="font-size: 25px;text-align: center;">
                        Tens a certeza que desejas excluir ?
                    </p>
                </div>
                <div class="modal-footer">


                    {!! Form::open(['route' => ['budget_articles.delete']]) !!}
                    {{ Form::bsCustom('id', $budget->name ?? null, ['type' => 'number']) }}

                    <button type="submit" class="btn btn-danger" style="border-radius:5px;">Eliminar</button>
                    {!! Form::close() !!}
                    <a href="#" type="button" data-dismiss="modal" class="btn btn-info"
                        style="border-radius:5px;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-panel" style="padding:0">
        @include('GA::budget.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('budget.index') }}">Orçamentos</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('budget_chapter.budget',$chapter->id_orcamento) }}">{{$chapter->nome_orcamento}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{$chapter->code_capitulo}}. {{$chapter->nome_capitulo}}
                                    
                                </li>

                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1>Artigos de ORÇAMENTOS</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('budget_articles.create',$chapter->id_capitulo) }}" class="btn btn-success  mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>
                        {{-- <button type="button" class="btn alert-primary" data-toggle="modal" data-target="#ModalSession">
                            <i class="fas fa-key"></i> Terminar sessão
                          </button> --}}


                        <table id="budget-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    {{-- <th>Orçamento</th>
                                    <th>Capítulo Nº</th>--}}
                                    <th>Artigo Nº </th> 
                                    <th>Descrição</th> 
                                    <th>Unidade</th>
                                    <th>Quantidade</th>
                                    <th>Unitário</th>
                                    {{-- <th>Total</th> --}}
                                    <th>Criado por</th>
                                    <th>Criado a</th>
                                    <th>Actualizado por</th>
                                    <th>Actualizado a</th>
                                    <th>Actividade</th> 
                                </tr>
                            </thead>
                        </table>

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

    <script>
        $(function() {
            $('#budget-table').DataTable({
                ajax: '{!! route('budget_articles.ajax',$chapter->id_capitulo) !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'code',
                    name: 'code'
                }, {
                    data: 'description',
                    name: 'description'
                }, {
                    data: 'unidade',
                    name: 'unidade'
                },{
                    data: 'quantidade',
                    name: 'quantidade'
                }, {
                    data: 'unitario',
                    name: 'unitario'
                },
                //  {
                //     data: 'total',
                //     name: 'total'
                // },
                 {
                    data: 'created_by',
                    name: 'created_by',
                    visible: false
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'updated_by',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }, {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }],
                searching: true,
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>

    <script>
        $("form div").css({display:"none"});
        
        function pegar(element) {

            var id = $(element).attr("data");

            id = parseInt(id);

            
            $("input[name='id']").val(id);
        }
        
    </script>

@endsection
