<title>Mudança de curso | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title')
    Disciplinas equivalentes({{ $tb_courses_change->curso_velho }} - {{ $tb_courses_change->curso_novo }})
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrícula</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Listagem dos estudantes</li>
@endsection
@section('body')
<table id="change-curso-table" class="table table-striped table-hover">
    <thead>
        <tr>
            <th id="dado">#</th>
            <th>disciplina [primeira]</th>
            <th>disciplina [segundo]</th>
            <th>Accões</th>
        </tr>
    </thead>
</table>
@endsection
@section('models')
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('change.courses.disciplina.del', $tb_courses_change->id) }}" class="modal-content"
                method="POST">
                @method('DELETE')
                @csrf
                <input type="hidden" id="id_tb_equivalence" name="id_tb_equivalence">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apagar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Com certeza que desejas apagar este registo?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">cancelar</button>
                    <button type="submit" class="btn btn-primary">confirma</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts-new')
    <script>
        $(function() {
            let dataTable = $('#change-curso-table').DataTable({
                ajax: "{{ route('change.courses.disciplina.list.ajax', $tb_courses_change->id) }}",
                buttons: [
                    'colvis',
                    'excel',
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'disciplina_first',
                    name: 'disciplina_first'
                }, {
                    data: 'disciplina_second',
                    name: 'disciplina_second'
                }, {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }]
            });
            dataTable.page('first').draw('page');
        });
    </script>
@endsection
