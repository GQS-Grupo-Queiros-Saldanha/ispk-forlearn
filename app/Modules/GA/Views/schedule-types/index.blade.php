@section('title',__('Tipos de horário'))
@extends('layouts.generic_index_new')
@section('page-title', 'Tipos de horário')
@section('breadcrumb')
    <!-- <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li> -->
    <li class="breadcrumb-item">
        <a href="{{ route('schedules.index') }}">Horários</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Tipos de horário</li>
@endsection
@section('body')
<table id="schedule_types-table" class="table table-striped table-hover">
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
    

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts-new')
  
    <script>
        $(function () {
            $('#schedule_types-table').DataTable({
                ajax: '{!! route('schedule-types.ajax') !!}',
                buttons:['colvis','excel'
                ,
                {
                            text: '<i class="fas fa-plus-square"></i>  @lang('common.new')',
                            className: 'btn-primary main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) {
                               
                                let url =  '{{ route('schedule-types.create') }}';
                                window.open(url, "_self");
                            }
                        }
                ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'stt.display_name'
                }, {
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
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
