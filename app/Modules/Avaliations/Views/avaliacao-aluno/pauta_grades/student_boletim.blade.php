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
                $("#table_student").html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando boletim...</p></div>');
                
                $.ajax({
                    url: "/pt/get_boletim_student/" + lective_year,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    var matricula = data.matricula;
                    var dados = data.dados;
                    var disciplinas = data.disciplinas;
                    var matriculationId = data.id;

                    if (!disciplinas || disciplinas.length === 0) {
                        $("#table_student").html("<div class='alert alert-info'><h5>Sem disciplinas associadas à matrícula</h5></div>");
                        return;
                    }

                    // Botão de download do boletim
                    $("#table_student").html('<a href="/boletim_pdf/' + matriculationId + '" class="btn btn-primary mb-3" target="_blank"><i class="bi bi-download me-2"></i>Baixar Boletim em PDF</a>');

                    // Separar disciplinas por semestre
                    var semestres = {1: [], 2: []};
                    disciplinas.forEach(function(d) {
                        var sem = parseInt(d.disciplinas[3]);
                        if (sem === 1) semestres[1].push(d);
                        else if (sem === 2) semestres[2].push(d);
                    });

                    // Loop pelos semestres
                    for (var num_semestre in semestres) {
                        var disciplinas_semestre = semestres[num_semestre];
                        if (disciplinas_semestre.length === 0) continue;

                        var html = '<div class="table-responsive mb-4">';
                        html += '<table class="table tabela_pauta table-striped table-hover table-sm">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<td colspan="3" class="boletim_text bg-dark text-white">';
                        html += '<b>' + matricula.nome_curso + '</b> | ';
                        html += 'Ano: <b>' + matricula.ano_curricular + 'º</b> | ';
                        html += 'Semestre: <b>' + num_semestre + 'º</b> | ';
                        html += 'Turma: <b>' + matricula.nome_turma + '</b>';
                        html += '</td>';
                        html += '<td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>';
                        html += '<td colspan="2" class="text-center bg1 p-top">EXAME</td>';
                        html += '<td colspan="2" class="text-center cf1 bo1 p-top">CLASSIFICAÇÃO</td>';
                        html += '<td colspan="4" class="rec bo1 text-center p-top">EXAME</td>';
                        html += '<td colspan="2" class="fn bo1 text-center p-top">CLASSIFICAÇÃO FINAL</td>';
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

                        disciplinas_semestre.forEach(function(disciplina, index) {
                            var pf1 = null, pf2 = null, oa = null, ex_escrito = null, ex_oral = null, nota_recurso = null;

                            // Processar notas
                            if (dados && Array.isArray(dados)) {
                                dados.forEach(function(nota) {
                                    if (nota.disciplina == disciplina.disciplinas) {
                                        if (nota.metrica == 'PP1') pf1 = parseFloat(nota.nota);
                                        if (nota.metrica == 'PP2') pf2 = parseFloat(nota.nota);
                                        if (nota.metrica == 'OA') oa = parseFloat(nota.nota);
                                        if (nota.metrica == 'Exame Escrito') ex_escrito = parseFloat(nota.nota);
                                        if (nota.metrica == 'Exame Oral') ex_oral = parseFloat(nota.nota);
                                        if (nota.metrica == 'Recurso') nota_recurso = parseFloat(nota.nota);
                                    }
                                });
                            }

                            // Média MAC
                            var media = null;
                            if (pf1 !== null || pf2 !== null || oa !== null) {
                                media = +((pf1 || 0) * 0.35 + (pf2 || 0) * 0.35 + (oa || 0) * 0.3).toFixed(2);
                            }

                            // CORREÇÃO DA LÓGICA DAS CORES - Igual ao blade
                            var cor_media = '', classificacao = '-';
                            if (media !== null) {
                                if (media >= 10.3) { 
                                    classificacao = 'Aprovado(a)'; 
                                    cor_media = 'for-green text-white';  // Verde para aprovado
                                } else if (media == 10) { 
                                    classificacao = 'Exame'; 
                                    cor_media = 'for-yellow';  // Amarelo para exame
                                } else { 
                                    classificacao = 'Recurso'; 
                                    cor_media = 'for-red';  // Vermelho para recurso
                                }
                            }

                            // Exame normal
                            var exame_total = null;
                            if (ex_escrito !== null || ex_oral !== null) {
                                exame_total = +((ex_escrito || 0) + (ex_oral || 0)).toFixed(2);
                            }

                            var media_exame = null;
                            if (media !== null && exame_total !== null) {
                                media_exame = +((media * 0.7) + (exame_total * 0.3)).toFixed(2);
                            }

                            // Média final - CORREÇÃO DA LÓGICA
                            var media_final = null;
                            if (media !== null) {
                                if (media < 10 && nota_recurso !== null) {
                                    // Se média MAC é menor que 10 e tem recurso, usa recurso
                                    media_final = nota_recurso;
                                } else if (media_exame !== null) {
                                    // Se tem exame, usa média exame
                                    media_final = media_exame;
                                } else {
                                    // Caso contrário, usa média MAC
                                    media_final = media;
                                }
                            }

                            // Classificação final - CORREÇÃO DA LÓGICA
                            var cor_final = '', estado_final = '-';
                            if (media_final !== null) {
                                if (media_final >= 10) { 
                                    estado_final = 'Aprovado(a)'; 
                                    cor_final = 'for-green text-white';  // Verde para aprovado final
                                } else { 
                                    estado_final = 'Reprovado(a)'; 
                                    cor_final = 'for-red';  // Vermelho para reprovado final
                                }
                            }

                            // Linha da disciplina
                            html += '<tr>';
                            html += '<td class="text-center">' + (index + 1) + '</td>';
                            html += '<td class="text-center">' + disciplina.disciplinas + '</td>';
                            html += '<td>' + disciplina.nome_disciplina + '</td>';
                            
                            // Notas MAC
                            html += '<td class="text-center">' + (pf1 !== null ? pf1 : '-') + '</td>';
                            html += '<td class="text-center">' + (pf2 !== null ? pf2 : '-') + '</td>';
                            html += '<td class="text-center">' + (oa !== null ? oa : '-') + '</td>';
                            
                            // Média MAC e classificação
                            html += '<td class="text-center fw-bold">' + (media !== null ? media : '-') + '</td>';
                            html += '<td class="text-center fw-bold ' + cor_media + '">' + classificacao + '</td>';
                            
                            // Exames
                            html += '<td class="text-center">' + (ex_escrito !== null ? ex_escrito : '-') + '</td>';
                            html += '<td class="text-center">' + (ex_oral !== null ? ex_oral : '-') + '</td>';
                            
                            // Média com exame e classificação (repetida)
                            html += '<td class="text-center">' + (media_exame !== null ? media_exame : '-') + '</td>';
                            html += '<td class="text-center fw-bold ' + cor_media + '">' + classificacao + '</td>';
                            
                            // Recursos
                            html += '<td colspan="2" class="text-center">' + (nota_recurso !== null ? nota_recurso : '-') + '</td>';
                            html += '<td colspan="2" class="text-center">-</td>';
                            
                            // Final
                            html += '<td colspan="2" class="text-center fw-bold">' + (media_final !== null ? media_final : '-') + '</td>';
                            html += '<td colspan="2" class="text-center fw-bold ' + cor_final + '">' + estado_final + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        $("#table_student").append(html);
                    }
                    
                    // Adicionar alguns estilos modernos
                    $("#table_student table").addClass("table-hover");
                    $("#table_student th").addClass("align-middle");
                    $("#table_student td").addClass("align-middle");
                    
                }).fail(function(xhr, status, error) {
                    $("#table_student").html('<div class="alert alert-danger">Erro ao carregar o boletim. Tente novamente.</div>');
                    console.error("Erro AJAX:", status, error);
                });
            }
        });
    </script>
@endsection
