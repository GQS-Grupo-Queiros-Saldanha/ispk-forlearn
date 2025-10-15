@section('title',__('GA::study-plan-editions.study_plan_editions'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content') 

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('GA::study-plan-editions.study_plan_editions')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('study-plan-editions') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('study-plan-editions.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="float-right mr-4" style="width:200px; !important">
                            <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
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

                        <div class="card">
                            <div class="card-body">

                                <table id="study-plan-editions-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('common.start_date')</th>
                                        <th>@lang('common.end_date')</th>
                                        <th>Ano lectivo</th>
                                        {{-- <th>@lang('GA::study-plan-editions.max_enrollments')</th> --}}
                                        {{-- <th>@lang('GA::study-plan-editions.block_enrollments')</th> --}}
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


<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Duplicar edição de plano de estudo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form action="{{ route('duplicate.study_plan')}}" method="POST">
            @csrf
            <div hidden>
                <input type="text" id="std_id" name="id">
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="Nome">Nome</label>
                        <input type="text" id="language_display_name" name="language_display_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="Descrição">Descrição</label>
                        <input type="text" id="language_description" name="language_description" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="Abreviação">Abreviação</label>
                        <input type="text" id="language_abreviation" name="language_abreviation" class="form-control">

                    </div>
                </div>
                <div class="mt-3">

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="Data de inicio">Data de inicio</label>
                         <input type="date" id="start_date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="Data de fim">Data de fim</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="Ano lectivo">Ano lectivo</label>
                        <select id="lective_year" name="lective_year" class="form-control ">
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Confirmar</button>
      </div>
       </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')

    @parent
    <script>
        $(function () {
       
            $('#study-plan-editions-table').DataTable({
                ajax: '{!! route('study-plan-editions.ajax') !!}',
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'display_name',
                    name: 'spet.display_name'
                }, {
                    data: 'start_date',
                    name: 'start_date'
                }, {
                    data: 'end_date',
                    name: 'end_date'
                },{
                    data: 'lective_year',
                    name: 'lyt.display_name'
                }, 
                {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
              
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });


             $("#lective_years").change(function(){

                var lective_year = $("#lective_years").val();


                $('#study-plan-editions-table').DataTable().clear().destroy();

                $('#study-plan-editions-table').DataTable({
                "ajax": {
                "url": "/gestao-academica/study-plan-editions/study-plan-editions-by-year/"+lective_year,
                "type": "GET",
                "data": {
                    "user_id": 451
                }
                },
                buttons:[
                    'colvis', 
                    'excel'
                ],
                columns: [{
                    data: 'display_name',
                    name: 'spet.display_name'
                }, {
                    data: 'start_date',
                    name: 'start_date'
                }, {
                    data: 'end_date',
                    name: 'end_date'
                },{
                    data: 'lective_year',
                    name: 'lyt.display_name'
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

            })


        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
        
    </script>
@endsection
