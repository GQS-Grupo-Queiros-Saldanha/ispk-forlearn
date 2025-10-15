
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection
<title>Recursos humanos | forLEARN® by GQS</title>
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
    .fotoUserFunc{
        border-radius: 50%;
        background-color: #c4c4c4;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: 50%;
        width: 150px;
        height: 150px;
        -webkit-filter: brightness(.9);
        filter: brightness(.9);
        border: 5px solid #fff;
        -webkit-transition: all .5s ease-in-out;
        transition: all .5s ease-in-out;
    }
</style>

<div class="content-panel">

    
    <div class="content-header">
        @include('RH::index_menu')
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>{{$data['action']}}</h1>
                   
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-0">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                        <h5 class="col-md-12 mb-2 text-right text-muted text-uppercase"><i class="fas fa-user-group"></i> Recurso humanos</h5>
                        
                        <div class="col-md-8 ">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Cargos</label>
                                <select id="roles" name="categoria" class="selectpicker form-control form-control-sm" data-actions-box="true" data-live-search="true"  data-selected-text-format="values" tabindex="-98">
                                    <option selected value=""></option>
                                    @php $i=0; @endphp
                                    @foreach ($data['roles'] as $role)
                                        @php $i++; @endphp
                                        <option value="{{$role->id}}">
                                            {{$role->currentTranslation->display_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- charts sobre dados RH --}}
                        <div class="col-md-4 mt-3 row pr-0">
                            <div class="col mb-2 pl-0 mr-0">
                                <div style="background: #20c7f9" class="p-0 small-box  text-white rounded">
                                    <div class="row pl-2 pt-2">
                                        <div class="inner col-md-9">
                                            <h2 id="totalLiuidade">{{$i}}</h2>
                                            <p>Cargos activos</p>
                                        </div>
                                        <div class="icon">
                                            <i style="font-size: 4pc; opacity: 0.2;" class="fas fa-user-tie"></i>
                                        </div>
                                    </div>
                                    <div style="background: #46434329" class="mt-3 mb-0 pb-0">
                                        <p href="#" class="small-box-footer text-center">Actúalizado a: @php $data=date('Y-m-d H:i:s'); echo "$data"; @endphp <i class="fas fa-date"></i></p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        {{--container perfil do funcionario --}}
                        @if (isset($dataUser))
                            <div id="perfiUser"  class="col-md-5 mt-4">
                                <div class="card card-widget widget-user">
                                    <div style="background: #20c7f9"  class="widget-user-header">
                                        <div class="m-3">
                                            <h4 class="widget-user-username">{{$dataUser->name}}</h4>
                                            <h6 class="widget-user-desc">Founder &amp; <b>CEO</b></h5>
                                        </div> 
                                    </div>
                                   
                                    <div class="widget-user-image text-center">
                                        <center>
                                            <div class="fotoUserFunc mt-1 mb-1" style="background-image: url('//forlearn.ao/storage/attachment/{{$dataUser->image}}');"></div>
                                        </center> 
                                    </div>
                                    <div style="background: #f3f3f3" class="card-footer">
                                        <div class="row">
                                            <div class="col-auto border-right">
                                                <div class="description-block">
                                                    @php $idade=$dataUser->idade==(int)date('Y') ? " N/A " : $dataUser->idade  @endphp
                                                    <h5 class="description-header text-center"> {{$idade}}</h5>
                                                    <span class="description-text"><b>IDADE</b></span>
                                                </div>
                                            </div>
                                            <div class="col-auto border-right">
                                                <div class="description-block text-center">
                                                    <h5 class="description-header text-center">{{$dataUser->telefone}}<b> / </b>{{$dataUser->whatsapp}}</h5>
                                                    <span class="description-text"><b>CONTACTOS</b></span>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="description-block">
                                                    <h5 class="description-header">35</h5>
                                                    <span class="description-text"><b></b></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7 pl-0 mt-4">
                                <canvas class="my-4 w-100 chartjs-render-monitor m-0 p-0"  id="myChart" width="1600" height="500" style="display: block; width: 1096px; height: 454px;"></canvas>
                            
                                <div class="m-0 p-0 mt-4  bd-highlight">
                                    <div class="card mb-3 border ">
                                        <div class="row no-gutters">
                                          <div class="p-4 m-0" style="background: #20c7f9; ">
                                            
                                          </div>
                                          <div class="m-0 p-0 ">
                                            <div class="card-body col-md-12 m-0 p-0 ml-1 mt-3 mb-2">
                                              <h5 class="card-title m-0 p-0  "></h5>
                                              <p class="card-text  m-0 p-0 ">: <b id="valorTransEstorno"></b></p>
                                              <p style="border-top: rgb(230, 222, 222) 0.5px solid" class="card-text m-0 p-0 "><small id="intervaloEstorno" class="text-muted"></small></p>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                </div>
                            </div>
                        @else
                        @endif
                    </div>
                    <div class="container-fluid ml-2  mr-2 mt-3">
                        <a href="{{ route('recurso_humano.gestaoStaff.pdf', ['id_role' => '']) }}" class="btn btn-secondary btn-sm mb-3" target="_blank" id="generate-pdf-link">
                                    @icon('fas fa-file-pdf')
                                    @lang('PDF')
                                </a>
                        <table id="users-table"  class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Cargo (s) </th>
                                    {{-- <th>Acções</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
         var getRoles=$("#roles");
         var ctx = document.getElementById("myChart");
  
        $(function () {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('recurso.getUsers') !!}',
                
                columns: [
                {
                    data: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false
                },{
                    data: 'users',
                    name: 'users'
                },{
                    data: 'email',
                    name: 'email'
                }, {
                    data: 'roles',
                    name: 'roles'
                }
                ],
                "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                    ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                },
            });
            
            $(document).ready(function () {

    var generateButton = $('#generate-pdf-link'); // Seletor do botão de gerar PDF

    // Esconde o botão inicialmente
    generateButton.hide();

    // Atualiza a exibição do botão quando a categoria é selecionada
    $('#roles').on('change', function () {
        var selectedRole = $(this).val(); // Pega o ID da categoria selecionada
        
        if (selectedRole && selectedRole !== "") {
            // Atualiza o link do botão com o ID da categoria selecionada
            var pdfLink = '{{ route('recurso_humano.gestaoStaff.pdf', ['id_role' => ':id_role']) }}';
            pdfLink = pdfLink.replace(':id_role', selectedRole); // Substitui ':id_role' pelo ID da categoria
            console.log(pdfLink); // Verifica a URL no console

            generateButton.attr('href', pdfLink); // Define o atributo href do botão
            generateButton.show(); // Mostra o botão se uma categoria válida for selecionada
        } else {
            generateButton.hide(); // Esconde o botão se nenhuma categoria for selecionada
        }
    });

    // Adiciona evento de clique ao botão de gerar PDF
    generateButton.on('click', function (e) {
        var selectedRole = $('#roles').val(); // Pega o ID da categoria selecionada

    });
});

            $("#roles").change(function (e) {
                console.log(getRoles.val())
                $('#users-table').DataTable().clear().destroy();
                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "ajax_users_by_role/"+getRoles.val(),
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        }, {
                            data: 'users',
                            name: 'users',
                            searchable: true
                        }, {
                            data: 'email',
                            name: 'email',
                            searchable: true
                        }, {
                            data: 'roles',
                            name: 'roles',
                            searchable: true
                        } 
                
                    ],
                    "lengthMenu": [ [10, 50, 100, 50000],  [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    },
                });

            });
            // var myChart = new Chart(ctx, {
            //     type: 'bar',
            //     data: {
            //         labels: ["outubro", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            //         datasets: [{
            //             data: [54, 21345, 18483, 24003, 23489, 24092, 12034],
            //             lineTension: 0,
            //             backgroundColor: '#e8f0fe',
            //             borderColor: '#007bff',
            //             borderWidth: 2,
            //             pointBackgroundColor: '#007bff'
            //         }]
            //     },
            //     options: {
            //         scales: {
            //             yAxes: [{
            //                 ticks: {
            //                     beginAtZero: false
            //                 }
            //             }]
            //         },
            //         legend: {
            //             display: false,
            //         }
            //     }
            // });
        })
        // Delete confirmation modal criar
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
