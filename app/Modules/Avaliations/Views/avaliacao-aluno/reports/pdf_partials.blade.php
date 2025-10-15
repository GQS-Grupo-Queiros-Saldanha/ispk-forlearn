
<table class="table">
    <thead>
        <th style="font-size: 8pt; border:1px solid #000;!important" class="text-center">Curso</th>
        <th style="font-size: 8pt;  border:1px solid #000;!important" class="text-center">Código</th>
        <th style="font-size: 8pt;  border:1px solid #000;!important" class="text-center">Ano Lectivo</th>
        <th style="font-size: 8pt;  border:1px solid #000;!important" class="text-center">Regime</th>
        <th style="font-size: 8pt;  border:1px solid #000;!important" class="text-center">Turma</th>
    </thead>
    <tbody>
        <tr>
            <td width="25%" style="font-size: 8pt; border:1px solid #000;!important" width="25%" style="font-size: 8pt;" class="text-center">
                {{ $discipline->course->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt; border:1px solid #000;!important" class="text-center">
                {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt; border:1px solid #000;!important" class="text-center"></td>
            <td width="25%" style="font-size: 8pt; border:1px solid #000;!important" class="text-center">
                {{ $discipline->study_plans_has_disciplines->first()->discipline_period->currentTranslation->display_name }}
            </td>
            <td width="25%" style="font-size: 8pt; border:1px solid #000;!important" class="text-center">
                {{ $class->code }}
            </td>
        </tr>
    </tbody>
</table>
    @php $x = 0; $i = 1; @endphp
     <table class="table">
        <thead id="head">

        </thead>

        <tbody id="body">

        </tbody>
    </table>
</div>

<div class="col-12">
    <table class="table-borderless">
        <thead>
            <th colspan="2" style="font-size: 9pt;">
                Assinaturas
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="font-size: 9pt;">Docente: ________________________________________________________________________.</td>
                <td style="font-size: 9pt;">Pelo gabinete de termos: ____________________________________________________________________.</td>
            </tr>
        </tbody>
    </table>
</div>

@section('scripts')
    @parent
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
                var resultAvaliacaos = {!! $avaliacaos !!};
                var resultStudents = {!! $students !!};
                var resultFinalGrades = {!! $finalGrades !!};
                var resultGrades = {!! $grades!!};
                var resultMetrics = {!! $metrics!!};
                var resultdisciplineHasMandatoryExam = {!! $disciplineHasMandatoryExam!!};

                 //var resultgradeWithPercentage_a = dataResult.gradeWithPercentage1;
                //var resultgradeWithPercentage_b = dataResult.gradeWithPercentage2;

                var resultgradesWithPercentage = {!! $gradesWithPercentage!!};

                var example = {!! $example !!};
                var head = '';
                var body = '';
                var i = 1;

                var y = 0;
                var x = 0;

                head += "<th style='border:1px solid #000;!important'> # </th>"
                head += "<th style='border:1px solid #000;!important'> Nº aluno</th>"
                head += "<th style='border:1px solid #000;!important'> Nome </th>"

                var m; for (m = 0; m < example.length; m++) {
                    x = x + 1;
                    x = x % resultMetrics.length;
                     if (example[m].metrica_nome !=  "Exame") {
                            head += "<th style='border:1px solid #000;!important'>"+ example[m].metrica_nome +"</th>";
                        }

                        if (example[m].avaliacaos_id != example[x].avaliacaos_id) {
                                head += "<th style='border:1px solid #000;!important'>"+ example[m].nome +"</th>";
                        }


                }
                 head += "<th style='border:1px solid #000;!important'>Classificação Final (CF)</th>"
                 head += "<th style='border:1px solid #000;!important'>Observações</th>"

                if (resultdisciplineHasMandatoryExam.exam == 1) {
                    var count = 1;
                 var a; for (a = 0; a < resultStudents.length; a++)
                 {
                    body += "<tr>"
                    body += "<td style='border:1px solid #000;!important'>"+ count++ +"</td><td style='border:1px solid #000;!important'>"+ resultStudents[a].student_number +"</td><td style='border:1px solid #000;!important'>"+resultStudents[a].user_name+"<input type='hidden' value="+resultStudents[a].user_id+" name='user_id[]'></td>";

                     var id_user = resultStudents[a].user_id;
                     var nota_final = 0;
                     var flag = true;

                     var m; for (m = 0; m < example.length; m++) {
                        x = x + 1;
                        x = x % resultMetrics.length;
                        var f; for (f = 0; f < resultGrades.length; f++) {
                            if (resultGrades[f].users_id == id_user && resultGrades[f].metricas_id == example[m].metrica_id) {
                                flag = false;

                                if (resultGrades[f].metricas_id != 55) {
                                    if (resultGrades[f].nota == null) {
                                        body += "<td style='border:1px solid #000;!important'> F </td>"
                                    }else{
                                        body += "<td style='border:1px solid #000;!important'>"+ resultGrades[f].nota +"</td>";
                                    }
                                }

                            }
                        }

                        if (example[m].avaliacaos_id != example[x].avaliacaos_id) {

                            var c; for (c = 0; c < resultFinalGrades.length; c++)
                                {
                                    if(resultFinalGrades[c].users_id == id_user && resultFinalGrades[c].avaliacaos_id == example[m].avaliacaos_id){
                                        flag = false;

                                        body += "<td style='border:1px solid #000;!important'>"+Math.round(resultFinalGrades[c].nota_final)+"</td>";
                                        if (example[m].avaliacaos_id == 21 && resultFinalGrades[c].nota_final < 6.5) {
                                            body += "<td style='border:1px solid #000;!important'> - </td>";
                                        }
                                    }
                                }
                            }

                        if(flag){
                            body += "<td style='border:1px solid #000;!important'> - </td>";
                        }
                }
                        var h; for (h = 0; h < resultgradesWithPercentage.length; h++) {
                            if (id_user == resultgradesWithPercentage[h].user_id) {
                                if (resultgradesWithPercentage[h].grade != null) {
                                    body += "<td style='border:1px solid #000;!important'>"+ Math.round(resultgradesWithPercentage[h].grade) +"</td>"

                                }
                                if (resultgradesWithPercentage[h].grade >= 10) {
                                    body += "<td style='border:1px solid #000;!important'>Aprovado (a)</td>"
                                }else{
                                    body += "<td style='border:1px solid #000;!important'>Recurso</td>"
                                }
                            }
                        }


                    body += "</tr>"
                 }

                }else if(resultdisciplineHasMandatoryExam.exam == 0){
                    var count = 1;
                    var a; for (a = 0; a < resultStudents.length; a++)
                 {
                    body += "<tr>"
                    body += "<td style='border:1px solid #000;!important'>" + count++ + "</td><td style='border:1px solid #000;!important'>"+ resultStudents[a].student_number +"</td><td style='border:1px solid #000;!important'>"+resultStudents[a].user_name+"<input type='hidden' value="+resultStudents[a].user_id+" name='user_id[]'></td>";

                     var id_user = resultStudents[a].user_id;
                     var nota_final = 0;
                     var flag = true;

                     var m; for (m = 0; m < example.length; m++) {
                        x = x + 1;
                        x = x % resultMetrics.length;
                        var f; for (f = 0; f < resultGrades.length; f++) {
                            if (resultGrades[f].users_id == id_user && resultGrades[f].metricas_id == example[m].metrica_id) {
                                flag = false;
                                //avaliar se a metrica for do exame... nao exibir, (estamos so a exibir a nota da avaliacao, nao a metrica)
                                if (resultGrades[f].metricas_id != 55) {
                                    if (resultGrades[f].nota == null) {
                                        body += "<td style='border:1px solid #000;!important'> F </td>"
                                    }else{
                                        body += "<td style='border:1px solid #000;!important'>"+ resultGrades[f].nota +"</td>";
                                    }
                                }

                            }
                        }

                        if (example[m].avaliacaos_id != example[x].avaliacaos_id) {

                            var c; for (c = 0; c < resultFinalGrades.length; c++)
                                {
                                    if(resultFinalGrades[c].users_id == id_user && resultFinalGrades[c].avaliacaos_id == example[m].avaliacaos_id){
                                        flag = false;

                                        body += "<td style='border:1px solid #000;!important'>" +Math.round(resultFinalGrades[c].nota_final)+ "</td>";
                                        if (example[m].avaliacaos_id == 21 && resultFinalGrades[c].nota_final >= 0 &&  resultFinalGrades[c].nota_final <= 6) {
                                            body += "<td style='border:1px solid #000;!important'> - </td>";
                                            body += "<td style='border:1px solid #000;!important'>"+ Math.round(resultFinalGrades[c].nota_final) +"</td>"
                                            body += "<td style='border:1px solid #000;!important'> Recurso </td>"

                                        }else if(example[m].avaliacaos_id == 21 && resultFinalGrades[c].nota_final >= 14 && resultFinalGrades[c].nota_final <= 20){
                                             body += "<td style='border:1px solid #000;!important'> - </td>";
                                             body += "<td style='border:1px solid #000;!important'>"+ Math.round(resultFinalGrades[c].nota_final) +"</td>"
                                             body += "<td style='border:1px solid #000;!important'> Aprovado (a) </td>"
                                        }else if(example[m].avaliacaos_id == 23 /*&& resultFinalGrades[c].nota_final >= 6.5 && resultFinalGrades[c].nota_final <= 13*/){

                                                    var h; for (h = 0; h < resultgradesWithPercentage.length; h++) {
                                                        if (id_user == resultgradesWithPercentage[h].user_id) {
                                                            if (resultgradesWithPercentage[h].grade != null) {
                                                                body += "<td style='border:1px solid #000;!important'>"+ Math.round(resultgradesWithPercentage[h].grade) +"</td>"

                                                            }
                                                            if (resultgradesWithPercentage[h].grade >= 10) {
                                                                body += "<td style='border:1px solid #000;!important'>Aprovado (a)</td>"
                                                            }else{
                                                                body += "<td style='border:1px solid #000;!important'>Recurso</td>"
                                                            }
                                                        }
                                                    }


                                        }
                                    }


                                }
                            }

                        if(flag){
                            body += "<td style='border:1px solid #000;!important'> - </td>";
                        }
                }



                    body += "</tr>"
                 }
                }


            $("#head").append(head);
            $("#body").append(body);



        });
    </script>
@endsection