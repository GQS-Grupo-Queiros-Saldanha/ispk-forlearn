@section('title',__('Payments::requests.requests'))
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Payments::requests.requests')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('requests') }}
                    </div>
                </div>
            </div>
        </div>
        <form action="{{ route('request_create') }}" method="post" id="data-check">
                @csrf
        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="col-6">

                        </div>


                            <hr>
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>@lang('Payments::requests.student'):</label>
                                            {{ $users[0]['display_name']}}
                                            <select name="user" id="user" hidden>
                                                @foreach ($users as $item)
                                                    <option value=" {{$item['id']}}">
                                                         {{$item['display_name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- Form::bsLiveSelect('user', $users, null, ['required', 'placeholder' => '']) --}}
                                        </div>
                                    </div>
                                    <div class="col-3">

                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label></label>
                                            <a href="" id="call-observation" target="_blank" class="btn btn-primary mt-3 mb-3">
                                                <i class="fas fa-plus-square"></i>
                                                Observações <span class="badge badge-pill badge-light rounded-circle" id="observation" style="font-size: 12pt;">0</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>


                        <div class="card">
                            <div class="card-body" id="content-table">
                            <div id="group">
                                     <table id="requests-table" class="table table-striped table-hover">
                                        <thead>
                                            <th>#</th>
                                            <th>#</th>
                                            <th>Emolumento / Propina</th>
                                            <th>Disciplina </th>
                                            <th>Valor Base</th>
                                            <th>Valor Taxas</th>
                                            <th>Estado</th>

                                        </thead>
                                            <tbody>

                                            </tbody>
                                    </table>
                                    </div>
                                    <div id="container">
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

@endsection

@section('scripts')
    @parent
    <script>

        var dataTableBaseUrl = '{{ route('requests.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;
        var transactionBtn = $('#transaction-btn');
        var selectUser = $('#user');
        var selectedUserId = null;

        $(function () {

            var userId = $("#user").val();
            $("#container").empty();
            listItens(userId);
            getStudentInfo(userId);
            countObservationsBy(userId);

            $("#motivo_estorno").val("");
            function getStudentInfo(userId){
                 let observationsRoute = ("{{ route('transaction_observations.show','id_user') }}").replace('id_user', userId);

                  $.get(observationsRoute, function (data) {
                    document.getElementById('call-observation').setAttribute('href', observationsRoute);
                 });
            }

        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        function countObservationsBy(userId)
        {
            $.ajax({
                    url: "/payments/count_Observations_by/" + userId,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response)
                    {
                        if(response > 0)
                        {
                            $("#observation").removeClass('badge-light');
                            $("#observation").addClass('badge-danger');
                        }else{
                             $("#observation").removeClass('badge-danger');
                             $("#observation").addClass('badge-light');
                        }

                        $("#observation").text(response);
                    }
                    });
        }

        function listItens(userId) {

            $.ajax({
                    url: "/payments/request_transaction_by/" + userId,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response)
                    {
                        //$('#exampleModalCenter').modal('hide');
                    }
                    }).done(
                        function(data)
                        {

                        //table.draw();
                            $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
                            $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)


                            // $('#requests-trans-table').DataTable({
                            //     "aoColumns" : [ null, null,null,null,null,null,null],
                            // });
                        }
                    )
        }
    </script>
@endsection
