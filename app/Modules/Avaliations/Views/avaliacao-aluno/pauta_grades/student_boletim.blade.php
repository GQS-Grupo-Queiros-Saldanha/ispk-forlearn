<title>BOLETIM DE NOTAS | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'BOLETIM DE NOTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Boletim de notas</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach 
        </select>
    </div>
@endsection
@section('body')
    <style>
        .boletim_text{
            font-weight: normal !important;
            font-size: 14px !important; 
        }
        .table{
            margin-bottom: 1px;
            padding-bottom: 1px;
        }
    </style>
    
   <div id="table_student" class="mt-2">
       
   </div>
@endsection

@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {

           

            
            getStudentBoletim($("#lective_year").val()); 
            
            $("#lective_year").change(function(){
                getStudentBoletim($(this).val());
            });
            
            function getStudentBoletim(lective_year) {
                $("#table_student").html(""); // limpa antes
                $.ajax({
                    url: "/pt/get_boletim_student/" + lective_year,
                    type: "GET",
                    dataType: "json"
                }).done(function(data) {
                    let percurso = data.percurso;

                    let tableHTML = `<table class="table tabela_pauta table-striped table-hover" id="tabela_pauta_student1">
                        <thead>
                            <tr>
                                <td colspan="3" class="boletim_text"><b>Engenharia em Pesquisa e Produção de Petróleo</b> | Ano: <b>1º</b> | Semestre: <b>1º</b> | Turma: <b>EP1M01</b></td>
                                <td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>
                                <td colspan="2" class="text-center bg1 p-top">EXAME</td>
                                <td class="text-center cf1 bo1 p-top" colspan="2">CLASSIFICAÇÃO</td>
                                <td class="rec bo1 text-center p-top" colspan="4">EXAME</td>
                                <td class="fn bo1 text-center p-top" colspan="2">CLASSIFICAÇÃO</td>
                            </tr>
                            <tr style="text-align:center">
                                <th class="bg1 bo1">#</th>
                                <th class="text-center small bg1 bo1">CÓDIGO</th>
                                <th class="bg1 bo1">DISCIPLINA</th>
                                <th class="bgmac bo1">PF1</th>
                                <th class="bgmac bo1">PF2</th>
                                <th class="bgmac bo1">OA</th>
                                <th colspan="2" class="bgmac bo1">MÉDIA</th>
                                <th class="bg1 bo1">ESCRITO</th>
                                <th class="bg1 bo1">ORAL</th>
                                <th class="cf1 bo1" colspan="2">MAC + EXAME</th>
                                <th class="rec bo1" colspan="2">RECURSO</th>
                                <th class="rec bo1" colspan="2">ESPECIAL</th>
                                <th class="fn bo1" colspan="2">FINAL</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    let counter = 1;
                    $.each(percurso, function(codigo, grupo) {
                        $.each(grupo, function(i, avl) {
                            tableHTML += `<tr>
                                <td>${counter++}</td>
                                <td>${avl.code_disciplina}</td>
                                <td>${avl.display_name}</td>
                                <td>${avl.PF1 ?? '-'}</td>
                                <td>${avl.PF2 ?? '-'}</td>
                                <td>${avl.OA ?? '-'}</td>
                                <td colspan="2">${avl.media ?? '-'}</td>
                                <td>${avl.escrito ?? '-'}</td>
                                <td>${avl.oral ?? '-'}</td>
                                <td colspan="2">${avl.mac_exame ?? '-'}</td>
                                <td colspan="2">${avl.recurso ?? '-'}</td>
                                <td colspan="2">${avl.especial ?? '-'}</td>
                                <td colspan="2">${avl.final ?? '-'}</td>
                            </tr>`;
                        });
                    });

                    tableHTML += `</tbody></table>`;

                    $("#table_student").html(tableHTML);
                });
            }


           
        })
    </script>
@endsection
