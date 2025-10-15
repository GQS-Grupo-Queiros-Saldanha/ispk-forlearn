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
                                @case('create') @lang('GA::discipline-absence-configuration.create_discipline_absence_configuration') @break
                                @case('show') @lang('GA::discipline-absence-configuration.discipline_absence_configuration') @break
                                @case('edit') @lang('GA::discipline-absence-configuration.edit_discipline_absence_configuration') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">

                            {{ Breadcrumbs::render('discipline-absence-configuration.edit', $study_plan_edition->translation) }}

                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                    {!! Form::model($study_plan_edition, ['route' => ['study-plan-editions.update_absences', $study_plan_edition->id], 'method' => 'put']) !!}
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-sm btn-success mb-3">
                            @icon('fas fa-save')
                            @lang('common.save')
                        </button>

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

                        <div class="card">
                            <div class="card">
                                <div class="card-body">
                                @lang('GA::study-plan-editions.study_plan_edition')
                                <h5 class="card-title mb-3">{{ $study_plan_edition->translation->display_name}}</h5>
                                    <table id="discipline_absences" data-display-length='100' class="table bordered-bottom table-striped table-hover"></table>
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

        let disciplinesList = {!! $disciplines_absences !!};

        function buildTable(data){
            let columns = ['Discipline','Total','Regime','Absences'];
            let html = "<thead><tr>";

            //get distinct disciplines
            let previousValue = 0, groupList = [];
            $.each(data, function(k, v){
                if(previousValue != v.discipline_id){

                    let result = $.grep(data, function(value){
                        return v.discipline_id == value.discipline_id
                    });

                    groupList.push(
                    {
                        total: result.length,
                        discipline_id: v.discipline_id
                    });
                }

                previousValue = v.discipline_id;

            });

            //create columns
            $.each(columns, function(k,v){
                html += "<th>" + v + "</th>";
            });

            html += "</tr></thead><tbody>";

            //create rows
            //disciplines
            let counter = 0;
            previousValue = 0;
            $.each(data,function(k,v){

                let checked = v.is_total ? "checked='checked'" : "";
                let activeTotal = v.is_total ? "" : "disabled='disabled'";
                let activeRegime = !v.is_total ? "" : "disabled='disabled'";

                let result = $.grep(groupList, function(value){
                    return v.discipline_id == value.discipline_id
                });

                rowspan = "";

                if(result[0].total > 1){
                    counter++;
                    rowspan = "rowspan='"+result[0].total+"'";
                }else{
                    counter = 0;
                }

                if(previousValue != v.discipline_id){
                    counter = 0;
                }

                previousValue = v.discipline_id;


                html += "<tr>";
                if(counter == 0){
                    html += "<td "+ rowspan+"><input type='hidden' name='ab_discipline[]' value='"+v.discipline_id+"'/>"+v.discipline+"</td>";
                    html += "<td "+ rowspan+"><input id='checkbox_discipline_"+v.discipline_id+"' class='checkbox-is-total' type='checkbox' name='ab_total["+v.discipline_id+"][]' " + checked + " value='"+v.is_total+"'/>"+
                        "<label for='checkbox_discipline_"+v.discipline_id+"' class='form-check-label'></label>"+
                        "<input min='1' max='99' class='input_is_total_"+v.discipline_id+"'  style='width: 50px;' type='number' name='ab_max_absence["+v.discipline_id+"][]' value='"+v.max_absences+"' "+activeTotal+"/></td>";
                }

                if(v.regime_id != null){
                    html += "<td><input type='hidden' name='ab_regime["+v.discipline_id+"][]' value='"+v.regime_id+"'/>"+v.discipline_regime+"</td>";
                    html += "<td><input min='1' max='99' class='input_regimes_"+v.discipline_id+"' style='width: 50px;' type='number' name='ab_max_absence_regime["+v.discipline_id+"][]' "+activeRegime+" value='"+(v.is_total != true ? v.max_absences : '' )+"'/></td>";
                }

                html += "</tr>";
            });

            html += "</tbody>";

            $("#discipline_absences").html(html);


        }

        $(document).ready(function(){
            buildTable(disciplinesList);

            $(document).on("click", ".checkbox-is-total", function(){
                let id = $(this).attr("id").replace("checkbox_discipline_", "");
                console.log($(this).siblings("input"));
                setTimeout(function(){
                    $(this).siblings("input").trigger("focus");
                },500);


                if($(this).is(":checked")){
                    $(".input_is_total_"+id).prop('disabled', false);
                    $(".input_regimes_"+id).prop('disabled', true);
                }else{
                    $(".input_is_total_"+id).prop('disabled', true);
                    $(".input_regimes_"+id).prop('disabled', false);
                }
            });
        });


    </script>
@endsection
