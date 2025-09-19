<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@php
$title = 'LANÇAR NOTAS DE ';
$title .= $type == 1 ? 'EXAME EXTRAORDINÁRIO' : 'EXAME DE MELHORIA'; 
@endphp
@section('page-title', $title)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page" style="text-transform:capitalize">{{ $title }}</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
    <style>
        .red {
            background-color: red !important;
        }
        #ConteudoMain {
            display: none;
        }
        .regime {
            width: 5%;
        }
        .dt-buttons {
            float: left;
            margin-bottom: 20px;
        }
        .dataTables_filter label {
            float: right;
        }
        .dataTables_length label {
            margin-left: 10px;
        }

        .devedor{
            background-color:red !important;
            color:white
        }
        .recurso{
            background-color:orange;
            color:white;
        }

        .dispensado{
            background-color:blue;
            color:white
        }

        .neen{
            background-color:green;
            color:white
        }
    </style>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
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
    {!! Form::open(['route' => ['melhoria-notas.store'],'id' => 'id_form_Nota']) !!}
    @csrf
                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione a disciplina</label>
                                        <select data-live-search="true" required
                                            class="selectpicker form-control form-control-sm" required=""
                                            id="Disciplina_id_Select" data-actions-box="false"
                                            data-selected-text-format="values" name="disciplina" tabindex="-98"></select>
                                    </div>
                                </div>    
                            </div> 
                        </div>
                        <div id="tabela_new" style="display: none;">
                            <div class="card  mr-2">
                                <div class="row">
                                    <div class="col-12">
                                        <h2 id="Titulo_Avalicao"></h2>
                                        <table class="table table-hover dark">
                                            <thead>
                                                <th class="text-center" style="width:40px;">#</th>
                                                <th class="text-center">PRESENÇA</th>
                                                <th class="text-center">MATRÍCULA</th>
                                                <th>ESTUDANTE</th>
                                                <th class="text-center">NOTA</th>
                                            </thead>
                                            <tbody id="students_new"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

    {!! Form::close() !!}

                    <div class="col-12">
                        <div id="div_btn_save" class=" float-right">
                            <span class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal"
                                data-target="#exampleModal">
                                <i class="fas fa-plus-circle"></i>
                                Guardar notas
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <a id="btn_pdf" class=" float-right" href="" target="_blank">
                            <span class="btn btn-primary mb-3 ml-3" 
                                >
                                <i class="fas fa-file-pdf"></i>
                                Gerar pdf
                            </span>
                        </a>
                    </div>
@endsection
@section('models')
    <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="exampleModalLabel">ALERTA | Confirmação de dados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="float-right">
                        <p class="text-danger" style="font-weight:bold; !important"> Caro DOCENTE
                            ({{ auth()->user()->name }}), as informações inseridas
                            neste<br> formulário ATRIBUIR NOTAS, são da sua inteira responsabilidade.
                            <br> Por favor seja rigoroso na informação prestada.
                        </p>
                    </div>
                  
    
                                
                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important">Verifique se os dados estão correctos, nomeadamente: </p>
                        <ul>
                            <li style="padding:5px; !important">
                                Falta algum aluno nesta PAUTA?
                            </li>
                        </ul>
                    </div>
                    <div style="margin-top:10px; !important">
                        <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações acima
                            assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.
                        </p>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima, por
                            favor seleccione: Tenho a certeza.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores
                        forLEARN</button>
                    <nav id="ocultar_btn">

                        <button type="button" class="btn btn-danger" id="btn-callSubmit">Tenho a certeza</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
            let lective_year = $("#lective_year");
            let Disciplina_id_Select = $("#Disciplina_id_Select");
            let course_id;
            let selectedLective = $('#lective_year').val();
            let id = '';
            let route;
           
            let lective = "<input type='hidden' name='selectedLective' id='selectedLective' class='form-control' value=" +
                            selectedLective + "> ";
                             $('#id_form_Nota').append(lective);

            let input = '<input id="type" type="hidden" name="type" value="{{$type}}">'
                             $("#id_form_Nota").append(input);
            //Carregar              
            ambiente();

            //Evento de mudança na select anolectivo
            lective_year.change(function() {
                selectedLective = lective_year.val();
                //chamndo a função de mudança de frames
                ambiente();
                $('#selectedLective').val(selectedLective);
            });

            function ambiente() {
                    discipline_get_new(selectedLective);
            }

            Disciplina_id_Select.change(function() {
                id = Disciplina_id_Select.val();
            
                if(id != ''){
                    $("#tabela_new").hide();
                    $("#students_new").empty();
                    route = '/grades/generate-pdf-grades/'+ id + '/' + selectedLective + '/' + $("#type").val();
                    $('#btn_pdf').attr('href', route);
                    StudantGrade(id, selectedLective);
                }
                else{
                    route = ''
                    $('#btn_pdf').attr('href', route);
                }
            });

            $("#btn-callSubmit").click(function() {
                
               $("#id_form_Nota").submit();
         
            });

     

            function discipline_get_new(anolectivo) {
                $.ajax({
                    url: "/pt/avaliations/disciplines_teacher/" + anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        console.log("Carregando as disciplinas...")
                    }
                }).done(function(data) {
                    if (data['disciplina'].length) {
                        $("#students_new").empty();
                        Disciplina_id_Select.prop('disabled', true);
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append(
                            '<option selected="" value="">Selecione a disciplina</option>');
                        $.each(data['disciplina'], function(index, row) {
                            $("#Disciplina_id_Select").append('<option  value="' +
                                row.course_id + ',' + row.discipline_id + ' ">#' + row
                                .code + '  ' + row.dt_display_name + '</option>');
                        });
                        Disciplina_id_Select.prop('disabled', false);
                        Disciplina_id_Select.selectpicker('refresh');
                       
                    } else {
                        Disciplina_id_Select.empty();
                        Disciplina_id_Select.prop('disabled', true);
                    }
                });
            }

            function StudantGrade(discipline_id, lective_year) {
               console.log($("#type").val())
                $.ajax({
                    url: "/grades/student_grades/" + discipline_id + "/" + lective_year + "/" + $("#type").val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {

                        $("#students_new tr").empty();

                        let resultStudents = dataResult.students;
                        let grades = dataResult.grades;
                        let Nota_aluno;
                        let bodyData = '';
                        let i = 1;
                        if (resultStudents.length > 0) {

                            resultStudents.forEach(function(student) {
                                    Nota_aluno = '';
                                 
                                    dataResult['grades'].forEach(function(nota) {
                                        if (student.user_id == nota.user_id) {
                                            Nota_aluno = nota.aanota;
                                            ausente = nota.ausente;
                                        }
                                    });

                                    if (Nota_aluno == null && ausente == 1) {
                                       
                                       //essa linhas é dos alunos que faltaram
                                       linha = "Linha_checado";
                                       bodyData +=
                                           '<tr  style="background-color:#f4f4f4;" id=' +
                                           linha + student.user_id + '>'
                                       bodyData += "<td>" + i++ +
                                           "</td><td width='120'><input name='inputCheckBox[]' checked value=" +
                                           student.user_id + "'  id='" + student.user_id +
                                           "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                           student.user_id +
                                           "' style='background: red; padding: 2px; color: #fff;'>AUSENTE</span></td>" + "<td width='120'>" + student
                                           .n_student + "</td> <td style='font-size:0.9pc'>" +
                                           student.user_name +
                                           "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                           student.user_id +
                                           "><input type='number'  readonly id='nota_checado" + student
                                           .user_id +
                                           "'  min='0' max='20' name='notas[]' class='form-control notaCampo' value=" +
                                           Nota_aluno + 
                                           ">";
                                       bodyData += '</tr>'
                                   } else {
                                       linha = "Linha_checado";
                                       bodyData += '<tr id=' + linha + student.user_id + '>'
                                       bodyData += "<td>" + i++ +
                                           "</td><td width='120'><input name='inputCheckBox[]' value='" +
                                           student.user_id + "'  id='" + student.user_id +
                                           "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                           student.user_id +
                                           "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span> </td> <td width='120'>" + student
                                           .n_student + "</td> <td style='font-size:0.9pc'>" +
                                           student.user_name +
                                           "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                           student.user_id +
                                           "><input type='number' id='nota_checado" + student
                                           .user_id +
                                           "'  min='0' max='20' name='notas[]' class='form-control notaCampo' value=" +
                                           Nota_aluno + 
                                           ">";
                                        
                                       bodyData += '</tr>'
                                   }

                            })

                        }
                        else {
                                bodyData += '<tr>'
                                bodyData +=
                                    "<td class='text-center fs-2'>Nenhum estudante foi encontrado nesta turma.</td>";
                                bodyData += '</tr>'
                            }

                         $("#students_new").append(bodyData);
                         $("#tabela_new").show();
                    }

                })

            }

            function verChecagem(element) {
    
            var linha1 = $("#Linha_checado" + element.id);
            var span = $("#span_checado" + element.id);
            var inputNota = $("#nota_checado" + element.id);
            let checkbox = document.getElementById('' + element.id);
      
            if (checkbox.checked) {
               
                linha1.css("background-color", "#f4f4f4");
                span.css("background-color", "red")
                span.text("AUSENTE");
                inputNota.val("");
                inputNota.prop("readonly",true);
            } else {
                linha1.css("background-color", "#fff");
                span.css("background-color", "#38C172")
                span.text("PRESENTE");
                inputNota.val("");
                inputNota.prop('disabled', false);
                inputNota.prop("readonly",false);
                checkbox.value = "";
            }
        }
    </script>
@endsection