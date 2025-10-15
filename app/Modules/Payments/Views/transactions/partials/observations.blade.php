<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Criar observações')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Observações</li>
@endsection
@section('body')

    {!! Form::open(['route' => 'transaction_observations.store', 'files' => true]) !!}
    @csrf
    <div class="row">
        <div class="col-6">
            <div class="form-group col">
                <label for="">Estudante</label>
                <input type="text" class="form-control" value="{{ $user->name }}" name="name" readonly>
                <input type="number" name="id" id="id" value="" hidden>
                <input type="number" name="user_id" value="{{ $user->id }}" hidden>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group col">
                <label for="">Anexar Arquivo</label>
                <input type="file" class="form-control" name="files">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group col">
                <label for="">Observação</label>
                <textarea name="observation" cols="20" rows="5" class="form-control" id="observation" required></textarea>
            </div>
        </div>
    </div>
    <div class="" id="criar" style="margin-left: 15px; !important">
        <button type="submit" class="btn btn-success" value="store" name="store">
            Salvar observação
        </button>
    </div>
    <div class="" id="editar" style="margin-left: 15px; !important" hidden>
        <button type="submit" class="btn btn-warning" name="edit" value="edit">
            Salvar edição
        </button>
        <button type="button" class="btn btn-primary" id="cancelarEdicao">
            Cancelar
        </button>
    </div>
    <div class="" id="novo" style="margin-left: 15px; !important" hidden>
        <button type="button" class="btn btn-primary" id="new" onclick="createObservation()">
            Nova observação
        </button>
    </div>
    {!! Form::close() !!}

    <table class="table table-hover table-striped mt-4">
        <thead>
            <th scope="col">Observação</th>
            <th scope="col">Arquivo anexado</th>
            <th scope="col">Download</th>
            <th scope="col" colspan="3">Ações</th>
        </thead>
        <tbody>
            @forelse ($observations as $observation)
                <tr>
                    <td>{{ $observation->observation }}</td>
                    <td>
                        @if ($observation->file != null)
                            {{ substr($observation->file, 20, strpos($observation->file, 'attachment/')) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($observation->file != null)
                            <a onclick="generateObservationArchive({{ $observation->id }})" href="#"
                                class="btn btn-primary btn-sm" style="margin: 5px; !important">
                                <i class="fas fa-download"></i>
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('transaction_observations.destroy', $observation->id) }}" method="post">
                            @method('delete')
                            @csrf

                            <button type="button" class="btn btn-info btn-sm" style="margin: 5px; !important"
                                onclick="showObservation({{ $observation->id }})">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button type="button" onclick="editObservation({{ $observation->id }})"
                                class="btn btn-warning btn-sm" style="margin: 5px; !important">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Deseja apagar observação?')" style="margin: 5px; !important">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            {{-- <button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
                                                                            data-action="{{ json_encode(['route' => ['transaction_observations.destroyObservation',  $observation->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
                                                                            type="submit">
                                                    <i class="fas fa-trash-alt"></i>
                                                    </button> --}}

                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sem observações</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
        function generateObservationArchive(id) {
            var myNewTab = window.open('about:blank', '_blank');
            let route = '{{ route('transactions.file', 0) }}'.slice(0, -1) + id
            $.ajax({
                method: "GET",
                url: route
            }).done(function(url) {
                myNewTab.location.href = url;
            });
        }

        function createObservation() {
            $("#observation").val("");
            $("#id").val("");
            $("#novo").prop('hidden', true);
            $("#criar").prop('hidden', false);
            $("#observation").prop('readonly', false);
            $("#editar").prop('hidden', true);
        }

        function showObservation(id) {
            $("#observation").val("");
            $("#id").val("");
            let route = '{{ route('transactions.showObservation', 0) }}'.slice(0, -1) + id

            $.ajax({
                method: "GET",
                url: route
            }).done(function(response) {
                bodyData = '';
                $("#id").val(id);
                var obj = jQuery.parseJSON(response);
                //bodyData += response;
                //$("#observation").append(bodyData);
                $("#observation").val(obj.observation);
                $("#observation").prop('readonly', true);
                $("#novo").prop('hidden', false);
                $("#criar").prop('hidden', true);
                $("#editar").prop('hidden', true);
            });
        }

        function editObservation(id) {
            $("#observation").val("");
            $("#id").val("");
            let route = '{{ route('transactions.showObservation', 0) }}'.slice(0, -1) + id

            $.ajax({
                method: "GET",
                url: route
            }).done(function(response) {
                bodyData = '';
                $("#criar").prop('hidden', true);
                $("#editar").prop('hidden', false);
                $("#novo").prop('hidden', true);
                $("#observation").prop('readonly', false);
                $("#id").val(id);
                var obj = jQuery.parseJSON(response);
                //bodyData += response;
                //$("#observation").append(bodyData);
                $("#observation").val(obj.observation);
            });
        }



        $("#cancelarEdicao").click(function() {
            $("#observation").val("");
            $("#id").val("");
            $("#criar").prop('hidden', false);
            $("#editar").prop('hidden', true);
            $("#observation").prop('readonly', false);
        });



        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection