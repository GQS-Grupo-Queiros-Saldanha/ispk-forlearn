@section('title',__('GA::summaries.summary'))
@extends('layouts.backoffice')

@section('content')


<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("Lessons::navbar.navbar")
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        @lang('GA::summaries.summary')
                    </h1>
                </div>
                <div class="col-sm-6"  style="padding-right: 30px">
                    <div class="float-right div-anolectivo">
                        <label>Selecione o ano lectivo</label>
                        <br>
                        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
                            style="width: 100%; !important">
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
            {{-- Form::bsLiveSelect('allDisciplines', $allDisciplines, ['required', 'placeholder' => '']) --}}
            <div class="col-6">
                <div class="form-group col">
                    <label>Selecione a disciplina</label>
                    <select data-live-search="true"  required class="selectpicker form-control form-control-sm" required="" id="disciplina" data-actions-box="false" data-selected-text-format="values" name="disciplina" tabindex="-98">

                    </select>

                </div>
            </div>
            <br>
            <div class="col-12">
                <div class="form-group col" >
                    <table class="table table-striped" id="table-content">
                        <thead>
                                <th>#</th>
                                <th>Disciplina</th>
                                <th>Regime de Disciplina</th>
                                <th>Nome</th>
                                <th>Ordenação</th>
                                <th>Criado a</th>
                                <th>Ações</th>
                            </thead>
                            <tbody id="body">

                            </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    @endsection

    @section('scripts')
    @parent
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
        //CKEDITOR.replace('summary-ckeditor');
        $(document).ready(function(){
            var lective_year = $("#lective_year").val();
            var curso = $("#disciplina")

            var all_discipline = @json($allDisciplines);
            
            // console.log(all_discipline);
            //console.clear();
            
            console.log(all_discipline);

            // CARREGA AS DISCIPLINAS
            function show_disciplina(all_discipline) {         
                    curso.empty();
                                            
                    curso.append('<option selected="" value="0">Selecione o curso</option>');
                    $.each(all_discipline, function (indexInArray, row) { 
                        $.each(row.disciplines, function (indexInArray2, row2) { 
                            curso.append('<option value="'+ row2.id+'">' + row2.current_translation.display_name + '</option>');
                        });
                    }); 
                    curso.prop('disabled', false);
                    curso.selectpicker('refresh');
            }
            
            if(all_discipline.length > 0){
                show_disciplina(all_discipline);
            }


            // AO MUDAR DE ANO LECTIVO
            $("#lective_year").change(function() {                
                lective_year = $("#lective_year").val();                
                
                $("#schedules-table").DataTable().destroy();
                
                var discipline_id = $(this).children("option:selected").val();

                console.log(discipline_id, lective_year)

                // CARREGA AS DISCIPLINAS
                $.ajax({
                    url: "/gestao-academica/summary_discipline_ajax/" + discipline_id + "/" + lective_year,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function (dataResult) {
                                                                    
                        if (dataResult[1].length>0) {

                            show_disciplina(dataResult[1]);

                        } 
                        else {
                            curso.empty();
                            curso.prop('disabled', false);
                            curso.selectpicker('refresh');
                        }  
                    },
                    error: function (dataResult) {                        
                        curso.empty();
                        curso.prop('disabled', false);
                        curso.selectpicker('refresh');
                    }
                });

                get_disciplina(discipline_id, lective_year)

            }); 


            // AO MUDAR A DISCIPLINA
            $("#disciplina").change(function() {
                var discipline_id = $(this).children("option:selected").val();
                console.log(discipline_id)
                get_disciplina(discipline_id, lective_year)
            })


            function get_disciplina(discipline_id, lective_year){
                    if (discipline_id == "") {
                        $("#body").empty();
                    }else{
                        $("#body").empty();
                        $.ajax({
                            url: "/gestao-academica/summary_discipline_ajax/" + discipline_id + "/" + lective_year,
                            type: "GET",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            cache: false,
                            dataType: 'json',

                            success: function (dataResult) {
                                                                            
                                // if (dataResult[1].length>0) {

                                //     show_disciplina(dataResult[1])

                                // }   

                                var bodyData = '';
                                var i = 1;
                                if(dataResult[0].length == 0){
                                    bodyData += '<tr>'
                                    bodyData += "<td colspan='7' class='text-center'>Disciplina Sem Sumários</td>"
                                    bodyData += '</tr>'
                                    $("#table-content").append(bodyData);
                                }else{
                                $.each(dataResult[0], function (index, row) {
                                    bodyData += '<tr>'
                                    bodyData += "<td>"+ i++ +"</td>"
                                    bodyData += "<td>" + row.discipline.current_translation.display_name + "</td>"
                                    bodyData += "<td>"+ row.regime.current_translation.display_name +"</td>"
                                    bodyData += "<td>" + row.translations[0].description + "</td>"
                                    bodyData += "<td>" + row.order + "</td>"
                                    bodyData += "<td>" + row.created_at + "</td>"
                                    bodyData += "<td><a href='summary_info/"+ row.id +"' class='btn btn-info btn-sm'><i class='fa fa-eye'></i></a></td>"
                                    bodyData += '</tr>'
                                })
                                $("#table-content").append(bodyData);
                                }

                                //$("#table-content").append(bodyData);
                                // console.log("BOM",dataResult);
                            },
                            error: function (dataResult) {
                                // console.log("ERRO",dataResult);
                                // curso.empty();
                                // alert('error' + result);
                            }
                        });
                    }
            }

        })
    </script>
    @endsection
