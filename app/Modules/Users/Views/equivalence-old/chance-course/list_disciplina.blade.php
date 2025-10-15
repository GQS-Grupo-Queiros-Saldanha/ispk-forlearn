@section('title', __('Mudança de curso'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('Users::equivalence.navbar')
        <section class="p-2">
            <div class="content-header">
                {{-- {{ Breadcrumbs::render('matriculations') }} <br> --}}
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Disciplinas equivalentes({{ $tb_courses_change->curso_velho }} -
                                {{ $tb_courses_change->curso_novo }})</h1>
                        </div>
                        <div class="col-sm-6">
                            {{-- {{ Breadcrumbs::render('matriculations') }} --}}

                            <div style="margin-left: 62%; width: 35%; margin-right: 2%;">
                                {{-- <div class="float-right" style="width: 300px; padding-top: 16px; padding-right: 0px; margin-right: 15px;"> --}}
                                {{-- <label for="lective_years">Selecione o ano lectivo</label>
                                <select name="lective_years" id="lective_years"
                                    class="selectpicker form-control form-control-sm">
                                    @foreach ($lectiveYears as $lectiveYear)
                                        @if ($lectiveYearSelected == $lectiveYear->id)
                                            <option value="{{ $lectiveYear->id }}" selected>
                                                {{ $lectiveYear->currentTranslation->display_name }}
                                            </option>
                                        @else
                                            <option value="{{ $lectiveYear->id }}">
                                                {{ $lectiveYear->currentTranslation->display_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Main content --}}
            <div class="content" style="margin-top: -1.5%; margin-left: -0.25%">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-auto">
                            {{-- 
                            <a id="group2" href="#" class="btn btn-success ml-4 mt-3 new_change_course">
                                <i class="fas fa-cog"></i>
                                Definir mudança
                            </a> --}}

                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{session('success')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{session('error')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                            <table id="change-curso-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th id="dado">#</th>
                                        <th>disciplina [primeira]</th>
                                        <th>disciplina [segundo]</th>
                                        {{-- <th>disciplina_novo</th> --}}
                                        <th>Accões</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form action="{{ route('change.courses.disciplina.del', $tb_courses_change->id) }}" class="modal-content" method="POST">
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

@section('scripts')
    @parent
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

            // Delete confirmation modal
            // Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}')

        });
    </script>
@endsection
