<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Gerir bolsa de estudo')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>        
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Gerir bolsa de estudo</li>
@endsection
@section('body')
    <table id="table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Estudante</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Entidade bolseira</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')

    <!-- Modal Lista de Bolseiros -->
    <div class="modal fade" id="modalScholarshipEntity" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selecionar Entidade Bolseira</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Entidade</label>
                        <select id="entity_id" class="form-control">
                            <option value="">-- Selecionar --</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}">
                                    {{ $entity->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnProceed">
                        Prosseguir
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            $('#table').DataTable({
                ajax: '{!! route('scholarship.ajax') !!}',
                buttons: ['colvis', 'excel',{
                        text: '<i class="fas fa-file-pdf"></i> Lista de bolseiros',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            //window.open("{{ route('pdf.scholarship-holder') }}", "_blank");
                            $('#modalScholarshipEntity').modal('show');
                        }
                    }
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'value',
                    name: 'u_p.value',

                }, {
                    data: 'name',
                    name: 'users.name'
                }, {
                    data: 'email',
                    name: 'users.email',

                }, {
                    data: 'display_name',
                    name: 'ct.display_name',

                }, {
                    data: 'company',
                    name: 'scholarship_entity.company',

                }],

                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
            $('#btnProceed').on('click', function() {

                let entityId = $('#entity_id').val();

                if (!entityId) {
                    alert('Selecione uma entidade.');
                    return;
                }

                let baseUrl = "{{ url('pdf-scholarship-holder') }}";
                let url = baseUrl + "/" + entityId;

                window.open(url, "_blank");

                $('#modalScholarshipEntity').modal('hide');
            });
        });
    </script>
@endsection
