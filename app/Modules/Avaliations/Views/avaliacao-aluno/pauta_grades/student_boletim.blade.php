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
        /* Mantenha seus estilos existentes */
        .tabela_pauta tbody tr td { font-weight: normal !important; } 
        .tabela_pauta tbody tr .text-bold { font-weight: 600 !important; } 
        .bg0 { background-color: #2f5496 !important; color: white; } 
        .bg1 { background-color: #8eaadb !important; } 
        .bg2 { background-color: #d9e2f3 !important; } 
        .bg3 { background-color: #fbe4d5 !important; } 
        .bg4 { background-color: #f4b083 !important; } 
        .bgmac { background-color: #a5c4ff !important; } 
        .cf1 { background-color: #4888ffdb !important; } 
        .rec { background-color: #a5c4ff !important; } 
        .fn { background-color: #1296ff !important; } 
        .bo1 { border: 1px solid white!important; } 
        table tr .small, table tr .small { font-size: 11px !important; } 
        .for-green { background-color: #00ff89 !important; } 
        .for-blue { background-color: #cce5ff !important; z-index: 1000; } 
        .for-red { background-color: #f5342ec2 !important; } 
        .for-yellow { background-color: #f39c12 !important; } 
        .boletim_text { font-weight: normal !important; } 
        .barra { color: #f39c12 !important; font-weight: bold; } 
        .semestreA, .semestre2{ } 
        
        /* Estilos adicionais para modernização */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .table thead {
            background-color: #2c3e50;
            color: white;
        }
        
        .table thead th {
            border-bottom: 2px solid #1a252f;
            font-weight: 600;
            padding: 10px 8px;
        }
        
        .table tbody tr {
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody td {
            padding: 8px;
            vertical-align: middle;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2f5496 0%, #4a6fa5 100%);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #254478 0%, #3d5c8a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(47, 84, 150, 0.3);
        }
        
        /* Melhorias para as cores de status */
        .for-green {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        .for-red {
            background-color: #dc3545 !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        .for-yellow {
            background-color: #ffc107 !important;
            color: #212529 !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .table {
                font-size: 0.75rem;
            }
            
            .table th, .table td {
                padding: 6px 4px;
            }
            
            .boletim_text {
                font-size: 12px !important;
            }
        }
    </style>
    
   <div id="table_studen" class="mt-2">
       @include('Cms::initial.components.manutencao')
   </div>
@endsection

@section('scripts-new')
    @parent
    <script>

        $(document).ready(function () {

            getStudentBoletim($("#lective_year").val());

            $("#lective_year").change(function () {
                getStudentBoletim($(this).val());
            });

            function getStudentBoletim(lective_year) {

                $("#table_student").html(
                    '<div class="text-center p-5">' +
                    '<div class="spinner-border text-primary"></div>' +
                    '<p class="mt-2">Carregando boletim...</p>' +
                    '</div>'
                );

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

                    $("#table_student").html(
                        '<a href="/boletim_pdf/' + matriculationId + '" ' +
                        'class="btn btn-primary mb-3" target="_blank">Baixar boletim</a>'
                    );

                    /* Separar disciplinas por semestre */
                    var semestres = {1: [], 2: []};

                    disciplinas.forEach(function (d) {
                        var sem = parseInt(d.disciplinas[3]);
                        if (sem === 1) semestres[1].push(d);
                        if (sem === 2) semestres[2].push(d);
                    });

                    /* Loop dos semestres */
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
                        html += '<td colspan="5" class="text-center bgmac bo1">MAC</td>';
                        html += '<td colspan="2" class="text-center bg1">EXAME</td>';
                        html += '<td colspan="2" class="text-center cf1 bo1">CLASSIFICAÇÃO</td>';
                        html += '<td colspan="4" class="rec bo1 text-center">EXAME</td>';
                        html += '<td colspan="2" class="fn bo1 text-center">CLASSIFICAÇÃO</td>';
                        html += '</tr>';

                        html += '<tr class="text-center">';
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

                        /* Loop disciplinas */
                        lista.forEach(function (disciplina, index) {

                            var pf1 = null, pf2 = null, oa = null;
                            var ex_escrito = null, ex_oral = null;
                            var nota_recurso = null;

                            dados.forEach(function (n) {
                                if (n.disciplina === disciplina.disciplinas) {
                                    if (n.metrica === 'PP1') pf1 = parseFloat(n.nota);
                                    if (n.metrica === 'PP2') pf2 = parseFloat(n.nota);
                                    if (n.metrica === 'OA') oa = parseFloat(n.nota);
                                    if (n.metrica === 'Exame Escrito') ex_escrito = parseFloat(n.nota);
                                    if (n.metrica === 'Exame Oral') ex_oral = parseFloat(n.nota);
                                    if (n.metrica === 'Recurso') nota_recurso = parseFloat(n.nota);
                                }
                            });

                            /* MÉDIA MAC — SÓ SE TODAS EXISTIREM */
                            var media = null;
                            if (pf1 !== null && pf2 !== null && oa !== null) {
                                media = +((pf1 * 0.35) + (pf2 * 0.35) + (oa * 0.3)).toFixed(2);
                            }

                            /* CLASSIFICAÇÃO MAC */
                            var cor_media = '';
                            var classificacao = '-';

                            if (media !== null) {
                                if (media >= 10.3) {
                                    classificacao = 'Aprovado(a)';
                                    cor_media = 'for-green';
                                } else if (media === 10) {
                                    classificacao = 'Exame';
                                    cor_media = 'for-yellow';
                                } else {
                                    classificacao = 'Recurso';
                                    cor_media = 'for-red';
                                }
                            }

                            /* EXAME */
                            var exame_total = null;
                            if (ex_escrito !== null || ex_oral !== null) {
                                exame_total = (ex_escrito || 0) + (ex_oral || 0);
                            }

                            var media_exame = null;
                            if (media !== null && exame_total !== null) {
                                media_exame = +((media * 0.7) + (exame_total * 0.3)).toFixed(2);
                            }

                            /* MÉDIA FINAL */
                            var media_final = null;
                            if (media !== null) {
                                if (media < 10 && nota_recurso !== null) {
                                    media_final = nota_recurso;
                                } else if (media_exame !== null) {
                                    media_final = media_exame;
                                } else {
                                    media_final = media;
                                }
                            }

                            var estado_final = '-';
                            var cor_final = '';

                            if (media_final !== null) {
                                if (media_final >= 10) {
                                    estado_final = 'Aprovado(a)';
                                    cor_final = 'for-green';
                                } else {
                                    estado_final = 'Reprovado(a)';
                                    cor_final = 'for-red';
                                }
                            }

                            html += '<tr>';
                            html += '<td class="text-center">' + (index + 1) + '</td>';
                            html += '<td class="text-center">' + disciplina.disciplinas + '</td>';
                            html += '<td>' + disciplina.nome_disciplina + '</td>';

                            html += '<td class="text-center">' + (pf1 !== null ? pf1 : '-') + '</td>';
                            html += '<td class="text-center">' + (pf2 !== null ? pf2 : '-') + '</td>';
                            html += '<td class="text-center">' + (oa  !== null ? oa  : '-') + '</td>';

                            html += '<td class="text-center">' + (media !== null ? media : '-') + '</td>';
                            html += '<td class="text-center ' + cor_media + '">' + classificacao + '</td>';

                            html += '<td class="text-center">' + (ex_escrito !== null ? ex_escrito : '-') + '</td>';
                            html += '<td class="text-center">' + (ex_oral !== null ? ex_oral : '-') + '</td>';

                            html += '<td class="text-center">' + (media_exame !== null ? media_exame : '-') + '</td>';
                            html += '<td class="text-center ' + cor_media + '">' + classificacao + '</td>';

                            html += '<td colspan="2" class="text-center">' + (nota_recurso !== null ? nota_recurso : '-') + '</td>';
                            html += '<td colspan="2" class="text-center">-</td>';

                            html += '<td colspan="2" class="text-center">' + (media_final !== null ? media_final : '-') + '</td>';
                            html += '<td colspan="2" class="text-center ' + cor_final + '">' + estado_final + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        $("#table_student").append(html);
                    }

                }).fail(function () {
                    $("#table_student").html(
                        '<div class="alert alert-danger">Erro ao carregar boletim</div>'
                    );
                });
            }
        });

    </script>

@endsection
