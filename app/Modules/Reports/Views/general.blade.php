@section('title',__('Utilizadores::Relatórios::ForLearn'))
@extends('layouts.backoffice_qbd')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Relatórios</h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </div>

        <div class="container-fluid">
                    <div class="card">
                        <div class="row">
                          <div class="col-4">
                            <div class="form-group col">
                                <label> Cargo: </label>
                                <select id="role" name="role" class="form-control">
                                    <option>...</option>
                                    <option value="6"> Estudante </option>
                                </select>
                                {{--  <button id="btn-get"
                                    style="width: 180px; background: #1e1e1e; color:#fff; padding:2px; border-color:#1e1e1e ; border-radius:7px;"><i
                                    class="fas fa-list-ul"></i> Gerar Relatório</button>
                                    --}}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group col">
                                <label> Estado: </label>
                                <select id="state" name="state" class="form-control">
                                    <option>...</option>
                                    <option value="matriculated"> Matrículado </option>
                                    <option value="non-matriculed"> Não matrículado </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group col">
                                <div hidden>
                                <label> Fotografia: </label>
                                <select id="photo" name="photo" class="form-control">
                                    <option>...</option>
                                    <option value="has-photo"> Com fotografia </option>
                                    <option value="non-photo"> Sem fotografia </option>
                                </select>
                                </div>
                                <label for="">Nº Mecanografico começa com...</label>
                                <input type="number" class="form-control" name="start_from" id="star_from">
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-4">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 m-3">
                                <button type="button" class="btn btn-primary" id="getReport">
                                    Gerar relátorio
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div id="group">
                                <table class="table table-striped table-hover data-table" id="data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Nº Mecanografico</th>
                                            <th>Nº Matricula
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Curso </th>
                                            <th>Fotografia</th>
                                            <th>Bilhete de identidade </th>
                                        </tr>
                                    </thead>
                                    <tbody id="body">
                                    </tbody>
                                </table>
                            </div>
                            <div id="containers"></div>
                        </div>

                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-keyboard="false"
                            data-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">

                                    <div class="modal-body">
                                        <center> <img src="/img/loading_gf.gif" width="100px"> <b>A Gerar
                                                Relatórios...</b> </center>
                                    </div>

                                </div>
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
            $('#exampleModalCenter').modal('hide');
            $("#getReport").click(function() {

                $('#exampleModalCenter').modal('show');
                //$("#body").empty();
                var role = $("#role").val();
                var state = $("#state").val();
                var photo = $("#photo").val();

                var start_from = $("#star_from").val();

                if (start_from == "") {
                    start_from = null;
                }

                console.log(start_from);

                $.ajax({
                url: "/reports/general-ajax/" + role + "/" + state + "/" + start_from,
                type: "GET",
                data: {
                _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',

                success: function (dataResult) {
                    //$("#body").ajax.reload();
                    $('#exampleModalCenter').modal('hide');
                    //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                    var bodyData = '';
                    var i = 1;
                    var a; for (a = 0; a < dataResult.length; a++) {
                        bodyData += '<tr>'
                            bodyData += "<td>"+ i++ +"</td><td>"+ dataResult[a].id +"</td><td>"+  dataResult[a].mecanografico+"</td><td>"+ dataResult[a].matriculation_code+"</td><td>"+dataResult[a].name+"</td><td>"+ dataResult[a].email +"</td><td>"+dataResult[a].course_name+"</td><td>"+ dataResult[a].photo+"<td>"+ dataResult[a].b_identidade+"</td>"
                        bodyData += '</tr>'
                    }
                    $("#body").append(bodyData);

                    console.log(dataResult);

                    $('#data-table').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
            {
                 extend: 'pageLength',
                 text: 'Mostrar Registos'
             },
             {
                 extend: 'colvis',
                 text: 'Colunas Visíveis <i class="fas fa-sort-down"></i>',
                 stateSave: true,
             },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-lg fa-file-pdf"></i>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-lg fa-print"></i>',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function(win){
                    $(win.document.body)
                    .css('font-size', '10pt')
                    .prepend(
                            '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'
                        );;
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            },
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-lg fa-copy"></i>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
        ]
                    });
                    },
                    error: function (dataResult) {
                    // alert('error' + result);
                    }

                    });
            })
        </script>
    @endsection