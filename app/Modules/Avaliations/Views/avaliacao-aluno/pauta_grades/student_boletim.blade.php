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
                $("#table_student").html(""); // limpa tabela
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

                    if (!disciplinas || disciplinas.length === 0) {
                        $("#table_student").html("<h1>Sem disciplinas associadas à matrícula</h1>");
                        return;
                    }

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

                        var html = '<table class="table tabela_pauta table-striped table-hover"><thead>';
                        html += '<tr><td colspan="3" class="boletim_text"><b>' + matricula.nome_curso + '</b> | Ano: <b>' + matricula.ano_curricular + 'º</b> | Semestre: <b>' + num_semestre + 'º</b> | Turma: <b>' + matricula.nome_turma + '</b></td>';
                        html += '<td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>';
                        html += '<td colspan="2" class="text-center bg1 p-top">EXAME</td>';
                        html += '<td colspan="2" class="text-center cf1 bo1 p-top">CLASSIFICAÇÃO</td>';
                        html += '<td colspan="4" class="rec bo1 text-center p-top">EXAME</td>';
                        html += '<td colspan="2" class="fn bo1 text-center p-top">CLASSIFICAÇÃO</td></tr>';

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
                        html += '</tr></thead><tbody>';

                        disciplinas_semestre.forEach(function(disciplina, index) {
                            var pf1 = pf2 = oa = ex_escrito = ex_oral = nota_recurso = null;

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

                            // Média MAC
                            var media = (pf1 !== null || pf2 !== null || oa !== null)
                                ? +( (pf1 || 0)*0.35 + (pf2 || 0)*0.35 + (oa || 0)*0.3 ).toFixed(2)
                                : null;

                            // Exame
                            var exame_total = (ex_escrito !== null || ex_oral !== null) ? ((ex_escrito||0) + (ex_oral||0)) : null;
                            var media_exame = (media !== null && exame_total !== null) ? +((media*0.7) + (exame_total*0.3)).toFixed(2) : null;

                            // Nota final considerando recurso
                            var media_final = (media < 10 && nota_recurso !== null) ? nota_recurso : (media_exame !== null ? media_exame : media);

                            // Classificações
                            var cor_media = '', cor_final = '';
                            var classificacao = '-', final = '-';
                            if (media !== null) {
                                if (media >= 10.3) { classificacao='Aprovado(a)'; cor_media='for-green'; }
                                else if (media == 10) { classificacao='Exame'; cor_media='for-yellow'; }
                                else { classificacao='Recurso'; cor_media='for-red'; }
                            }
                            if (media_final !== null) {
                                if (media_final >= 10) { final='Aprovado(a)'; cor_final='for-green'; }
                                else { final='Reprovado(a)'; cor_final='for-red'; }
                            }

                            html += '<tr>';
                            html += '<td class="text-center">'+(index+1)+'</td>';
                            html += '<td class="text-center">'+disciplina.disciplinas+'</td>';
                            html += '<td>'+disciplina.nome_disciplina+'</td>';
                            html += '<td class="text-center">'+(pf1!==null?pf1:'-')+'</td>';
                            html += '<td class="text-center">'+(pf2!==null?pf2:'-')+'</td>';
                            html += '<td class="text-center">'+(oa!==null?oa:'-')+'</td>';
                            html += '<td class="text-center">'+(media!==null?media:'-')+'</td>';
                            html += '<td class="text-center '+cor_media+'">'+classificacao+'</td>';
                            html += '<td class="text-center">'+(ex_escrito!==null?ex_escrito:'-')+'</td>';
                            html += '<td class="text-center">'+(ex_oral!==null?ex_oral:'-')+'</td>';
                            html += '<td class="text-center">'+(media_exame!==null?media_exame:'-')+'</td>';
                            html += '<td class="text-center '+cor_media+'">'+classificacao+'</td>';
                            html += '<td colspan="2" class="text-center">'+(nota_recurso!==null?nota_recurso:'-')+'</td>';
                            html += '<td colspan="2" class="text-center">-</td>';
                            html += '<td colspan="2" class="text-center">'+(media_final!==null?media_final:'-')+'</td>';
                            html += '<td colspan="2" class="text-center '+cor_final+'">'+final+'</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        $("#table_student").append(html);
                    }
                });
            }
           
        })
    </script>
@endsection
