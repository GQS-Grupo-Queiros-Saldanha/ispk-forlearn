@switch($action)
    @case('create') @section('title',__('GA::discipline-absence-configuration.create_discipline_absence_configuration')) @break
@case('show') @section('title',__('GA::discipline-absence-configuration.discipline_absence_configuration')) @break
@case('edit') @section('title',__('GA::discipline-absence-configuration.edit_discipline_absence_configuration')) @break
@endswitch

@extends('layouts.backoffice')

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('GA::discipline-absence-configuration.create_discipline-absence-configuration') @break
                                @case('show') @lang('GA::discipline-absence-configuration.discipline-absence-configuration') @break
                                @case('edit') @lang('GA::discipline-absence-configuration.edit_discipline-absence-configuration') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('discipline-absence-configuration.create') }} @break
                            @case('show') {{ Breadcrumbs::render('discipline-absence-configuration.show', $discipline_absence_configuration) }} @break
                            @case('edit') {{ Breadcrumbs::render('discipline-absence-configuration.edit', $discipline_absence_configuration) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['discipline-absence-configuration.store']]) !!}
                    @break
                    @case('show')
                        {!! Form::model($discipline_absence_configuration) !!}
                    @break
                    @case('edit')
                        {!! Form::model($discipline_absence_configuration, ['route' => ['discipline-absence-configuration.update', $discipline_absence_configuration->id], 'method' => 'put']) !!}
                    @break
                @endswitch

                <div class="row">
                    <div class="col">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    ×
                                </button>
                                <h5>@choice('common.error', $errors->count())</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @switch($action)
                            @case('create')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-plus-circle')
                                @lang('common.create')
                            </button>
                            @break
                            @case('edit')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                            <a href="{{ route('discipline-absence-configuration.edit', $discipline_absence_configuration->id) }}" class="btn btn-sm btn-warning mb-3">
                                @icon('fas fa-edit')
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="card spe-form">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">@lang('GA::study-plan-editions.discipline_absences')</h5>
                                    <table id="discipline_absences" data-display-length='100' class="table table-striped table-hover"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>

        let disciplinesList = json_encode({{ $disciplines_regimes }});

        function buildTable(data){
            let columns = ['Discipline','Total','Regime','Absences'];
            let html = "<thead><tr>";

            $.each(columns, function(k,v){
                html += "<th>" + v + "</th>";
            });

            html += "</tr></thead><tbody>";

            $.each(data,function(k,v){
                html += "<tr rowspan='3'>";
                html += "<td><input type='hidden' name='ab_discipline' value='"+v.discipline_id+"'"+v.discipline+"</td>";
                html += "<td><input type='hidden' name='ab_total' value='"+v.is_total+"'"+v.is_total+"</td>";

                $.each(data.regimes, function(j,l){
                    html += "<td><input type='hidden' name='ab_regime' value='"+l.regime_id+"'"+l.regime+"</td>";
                    html += "<td><input type='hidden' name='ab_absence' value='"+l.absence+"'"+l.absence+"</td>";
                });

                html += "</tr>";
            });

            html += "</tbody></table>";

        }


    </script>
@endsection
