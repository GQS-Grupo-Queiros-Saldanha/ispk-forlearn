<title>{{ 'Boletim_de_notas_'.$student_info->matricula .
    '_' .
    $student_info->lective_year }}
</title>
@extends('layouts.print')
@section('content')
@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
    $documentoCode_documento = 50;
    $doc_name = 'Boletim de notas';
    $discipline_code = '';
@endphp
<main>
    @include('Reports::pdf_model.forLEARN_header')
    <!-- aqui termina o cabeçalho do pdf -->
    <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te">

                        <tr class="bg1">
                            <th class="text-center">Estudante</th>
                            <th class="text-center">Matrícula</th>
                            <th class="text-center">E-mail</th>
                            <th class="text-center">Turma</th>
                            <th class="text-center">Curso</th>
                            <th class="text-center">Ano Lectivo</th>
                        </tr>
                        
                        <tr class="bg2">
                            <td class="text-center bg2">{{ $student_info->full_name }}</td>
                            <td class="text-center bg2">{{ $student_info->matricula }}</td>
                            <td class="text-center bg2">{{ $student_info->email }}</td>
                            <td class="text-center bg2">{{ $student_info->classe }}</td>
                            <td class="text-center bg2">{{ $student_info->course }}</td>
                            <td class="text-center bg2">{{ $student_info->lective_year }}</td>
                        </tr>

                    </table>
                </div>
            </div>
            <!-- personalName -->
            <style>
                .tabela_pauta tbody td {
                    padding: 7px !important;
                    padding: 7px !important;
                    font-size: 14px!important;
                    min-width:20px!important;
                }

                .tabela_pauta thead th {
                    padding: 7px !important;
                    padding: 7px !important;
                    font-size: 12px!important;
                    min-width: 20px!important;
                } 
                .boletim_text{
                    font-size: 14px!important;
                }
                table tr .small,
                table tr .small {
                    font-size: 14px!important;
                }

                .for-red {
                    background-color: rgba(245, 52, 46, 0.761)!important; 
                   
                }
                .cf1{ 
                    background-color: rgba(72, 136, 255, 0.859)!important;
                   
                }
                .p-top{
                    padding-top: 5px!important;font-size: 13px!important;
                }
                
          
                .text-f{
                    font-weight:normal!important;font-size: 11px!important;
                }

            </style>
            
            
            <div class="row">
                <div class="col-12">
                    <div class="">
                       
                        @include('Cms::initial.components.boletim')
                        <script>
        $(document).ready(function () {

            getStudentBoletim($("#lective_year").val());

            $("#lective_year").change(function () {
                getStudentBoletim($(this).val());
            });

            function getStudentBoletim(lective_year) {

                $.ajax({
                    url: "/pt/get_boletim_student/" + lective_year,
                    type: "GET",
                    dataType: "json"
                }).done(function (data) {

                    var matricula   = data.matricula;
                    var disciplinas = data.disciplinas;
                    var dados       = data.dados;
                    var matriculationId = data.id;

                    if (!disciplinas || disciplinas.length === 0) {
                        $("#table_student").html(
                            "<div class='alert alert-info'>Sem disciplinas associadas à matrícula</div>"
                        );
                        return;
                    }

                    // Separar disciplinas por semestre
                    var semestres = {1: [], 2: []};
                    disciplinas.forEach(function (d) {
                        var sem = parseInt(d.disciplinas[3]);
                        if (sem === 1) semestres[1].push(d);
                        if (sem === 2) semestres[2].push(d);
                    });

                    // Loop semestres
                    for (var num_semestre in semestres) {

                        var lista = semestres[num_semestre];
                        if (lista.length === 0) continue;

                        var html = '';
                        html += '<table class="table tabela_pauta table-striped table-hover">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<td colspan="3" class="boletim_text">';
                        html += '<b>' + matricula.nome_curso + '</b> ';
                        html += '<span class="barra">|</span> Ano: <b>' + matricula.ano_curricular + 'º</b> ';
                        html += '<span class="barra">|</span> Semestre: <b>' + num_semestre + 'º</b> ';
                        html += '<span class="barra">|</span> Turma: <b>' + matricula.nome_turma + '</b>';
                        html += '</td>';
                        html += '<td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>';
                        html += '<td colspan="2" class="text-center bg1 p-top">EXAME</td>';
                        html += '<td colspan="2" class="text-center cf1 bo1 p-top">CLASSIFICAÇÃO</td>';
                        html += '<td colspan="4" class="rec bo1 text-center p-top">EXAME</td>';
                        html += '<td colspan="2" class="fn bo1 text-center p-top">CLASSIFICAÇÃO</td>';
                        html += '</tr>';

                        html += '<tr style="text-align: center">';
                        html += '<th class="bg1 bo1">#</th>';
                        html += '<th class="bg1 bo1">CÓDIGO</th>';
                        html += '<th class="bg1 bo1">DISCIPLINA</th>';
                        html += '<th class="bgmac bo1">PF1</th>';
                        html += '<th class="bgmac bo1">PF2</th>';
                        html += '<th class="bgmac bo1">OA</th>';
                        html += '<th colspan="2" class="bgmac bo1">MÉDIA</th>';
                        html += '<th class="bg1 bo1">ESCRITO</th>';
                        html += '<th class="bg1 bo1">ORAL</th>';
                        html += '<th colspan="2" class="cf1 bo1">MAC + EXAME</th>';
                        html += '<th colspan="2" class="rec bo1">RECURSO</th>';
                        html += '<th colspan="2" class="rec bo1">ESPECIAL</th>';
                        html += '<th colspan="2" class="fn bo1">FINAL</th>';
                        html += '</tr>';
                        html += '</thead><tbody>';

                        // Loop disciplinas
                        lista.forEach(function (disciplina, index) {

                            var pf1 = null, pf2 = null, oa = null;
                            var ex_escrito = null, ex_oral = null;
                            var nota_recurso = null;

                            // Pega sempre a maior nota por métrica
                            dados.forEach(function (n) {
                                if (n.disciplina === disciplina.disciplinas && n.nota !== null) {
                                    var valor = parseFloat(n.nota);
                                    if (n.metrica === 'PP1') pf1 = pf1 === null ? valor : Math.max(pf1, valor);
                                    if (n.metrica === 'PP2') pf2 = pf2 === null ? valor : Math.max(pf2, valor);
                                    if (n.metrica === 'OA') oa = oa === null ? valor : Math.max(oa, valor);
                                    if (n.metrica === 'Exame Escrito') ex_escrito = ex_escrito === null ? valor : Math.max(ex_escrito, valor);
                                    if (n.metrica === 'Exame Oral') ex_oral = ex_oral === null ? valor : Math.max(ex_oral, valor);
                                    if (n.metrica === 'Recurso') nota_recurso = nota_recurso === null ? valor : Math.max(nota_recurso, valor);
                                }
                            });

                            // Média MAC só se todas existirem
                            var media = (pf1 !== null && pf2 !== null && oa !== null)
                                ? +((pf1*0.35) + (pf2*0.35) + (oa*0.3)).toFixed(2)
                                : null;

                            // Classificação MAC
                            var cor_media = '', classificacao = '-';
                            if (media !== null) {
                                if (media >= 10.3) { classificacao='Aprovado(a)'; cor_media='for-green'; }
                                else if (media === 10) { classificacao='Exame'; cor_media='for-yellow'; }
                                else { classificacao='Recurso'; cor_media='for-red'; }
                            }

                            // Exame
                            var exame_total = (ex_escrito !== null || ex_oral !== null) ? (ex_escrito||0)+(ex_oral||0) : null;
                            var media_exame = (media!==null && exame_total!==null) ? +((media*0.7)+(exame_total*0.3)).toFixed(2) : null;

                            // Média final
                            var media_final = null;
                            if (media !== null) {
                                if (media<10 && nota_recurso!==null) media_final = nota_recurso;
                                else if (media_exame!==null) media_final = media_exame;
                                else media_final = media;
                            }

                            var estado_final = '-', cor_final = '';
                            if (media_final !== null) {
                                if (media_final >= 10) { estado_final='Aprovado(a)'; cor_final='for-green'; }
                                else { estado_final='Reprovado(a)'; cor_final='for-red'; }
                            }

                            html += '<tr>';
                            html += '<td class="text-center">' + (index+1) + '</td>';
                            html += '<td class="text-center">' + disciplina.disciplinas + '</td>';
                            html += '<td>' + disciplina.nome_disciplina + '</td>';
                            html += '<td class="text-center">' + (pf1!==null?pf1:'-') + '</td>';
                            html += '<td class="text-center">' + (pf2!==null?pf2:'-') + '</td>';
                            html += '<td class="text-center">' + (oa!==null?oa:'-') + '</td>';
                            html += '<td class="text-center">' + (media!==null?media:'-') + '</td>';
                            html += '<td class="text-center '+cor_media+'">' + classificacao + '</td>';
                            html += '<td class="text-center">' + (ex_escrito!==null?ex_escrito:'-') + '</td>';
                            html += '<td class="text-center">' + (ex_oral!==null?ex_oral:'-') + '</td>';
                            html += '<td class="text-center">' + (media_exame!==null?media_exame:'-') + '</td>';
                            html += '<td class="text-center '+cor_media+'">' + classificacao + '</td>';
                            html += '<td colspan="2" class="text-center">' + (nota_recurso!==null?nota_recurso:'-') + '</td>';
                            html += '<td colspan="2" class="text-center">-</td>';
                            html += '<td colspan="2" class="text-center">' + (media_final!==null?media_final:'-') + '</td>';
                            html += '<td colspan="2" class="text-center '+cor_final+'">' + estado_final + '</td>';
                            html += '</tr>';

                        });

                        html += '</tbody></table>';
                        
                        
                        $("#table_student").append(html);

                        $("#table_student").append(
                        '<br><a class"d-flex justify-content-end mt-3" href="/boletim_pdf/' + matriculationId + '" ' +
                        'class="btn btn-primary mb-3" target="_blank"><i class="bi bi-filetype-pdf"></i>Boletim de Notas</a>');

                    }

                }).fail(function () {
                    $("#table_student").html(
                        '<div class="alert alert-danger">Erro ao carregar boletim</div>'
                    );
                });
            }
        });
    </script>

                        <br>
                        <br>

                        @include('Reports::pdf_model.signature')

                    </div>

                </div>
            </div>
            <style>
                 .bgmac{ 
                    background-color: rgba(72, 136, 255, 0.859) !important;
                }   
            </style>
        </div>
    </div>
    </div>
    </div>
</main>
@endsection

<script></script>
