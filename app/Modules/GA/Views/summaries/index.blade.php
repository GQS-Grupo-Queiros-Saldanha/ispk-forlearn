@section('title',__('GA::summaries.summaries'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("Lessons::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            {{ Breadcrumbs::render('summaries') }}
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('GA::summaries.summaries')</h1>
                    </div>
                    
                    <div class="col-sm-6" style="padding-right: 30px">
                        <div class="float-right div-anolectivo" style="width: 45%; !important">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
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
                            </select> 
                                                       
                        </div>                      
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('summaries.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">

                                <table id="summaries-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('GA::study-plans.study_plan')</th>
                                        <th>@lang('GA::disciplines.discipline')</th>
                                        <th>@lang('GA::discipline-regimes.discipline_regime')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('GA::summaries.order')</th>
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

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            var lective_year = $("#lective_year").val();
            
            $("#lective_year").change(function() {                
                lective_year = $("#lective_year").val();                
                
                $("#summaries-table").DataTable().destroy();
                // $('#summaries-table').clear().draw();
                
                get_summary(lective_year);

            });

            get_summary(lective_year);
            
        });

        function get_summary(id_lective_year) {
            $('#summaries-table').DataTable({
                "processing": true,
                "serverSide":true,
                ajax: {
                    url: "summaries_ajax/"+id_lective_year,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    // cache: false,
                    // dataType: 'json'
                },
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [
                    {data:'study_plan', name:'study_plan'},
                    {data:'discipline', name:'discipline'},
                    {data:'regime', name:'regime'},
                    {data:'display_name', name:'display_name'},
                    {data:'order', name:'order'},
                    {data:'created_by', name:'created_by'},
                    {data:'updated_by', name:'updated_by'},
                    {data:'created_at', name:'created_at'},
                    {data:'updated_at', name:'updated_at'},
                    {data:'actions', name:'actions'}
                ],
                 "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "Todos"]],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        }
        

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
