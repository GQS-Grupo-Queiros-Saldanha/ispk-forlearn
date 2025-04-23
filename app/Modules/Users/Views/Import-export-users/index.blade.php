<title>Importação | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Importação | exportação [Gestão de dados]')
@section('breadcrumb')


    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Importar e exportar </li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection

@section('body')
    <table id="change-curso-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th id="dado">#</th>
                <th>Nome completo</th>
                <th>e-mail</th>
                <th>Nº do bilhete</th>
                <th>Nº de matrícula</th>
                {{-- <th>Nº de matrícula Antigo</th> --}}
                <th>Curso</th>
                <th>Ano curricular</th>
                {{-- <th>Cargo</th> --}}
                <th>@lang('common.created_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::Import-export-users.modal')
    @include('Users::Import-export-users.modal_export')
    @include('Users::equivalence.chance-course.modal_equivalence_change')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            let btnAdd = $('#btn_add');
            let listEquivalencia = $('#list_equivalencia');
            let array1 = [];
            let array2 = [];
            let array_ids = [];
            let UsersReady = [];
          

            Tabela();


            $("#close_modal_create").click(function() {
                $("#CreateCursoChange").modal('hide');
            });

            let id_anoLective = $("#lective_years");

            id_anoLective.bind('change keypress', function() {
                id_anoLective = $("#lective_years").val();
     
            });


            $(".new_matricula").click(function(e) {
                id_anoLective = $("#lective_years").val();
                $(this).attr('href', 'confirmation_matriculation/create/' + id_anoLective);
            });

            $("#modal_create_save_ajax").click(function(e) {
                if (array_ids.length > 0) {
                    $.ajax({
                        url: "{{ route('courses.change.disciplina.store') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            items: array_ids
                        },
                        success: function(response) {
                            if (response.status && response.total == 'yes') {
                                alert('foi inserido com successo, todos items');
                            }
                            if (response.total == 'parcial') {}
                        }
                    });
                    array1 = [];
                    array2 = [];
                    array_ids = [];
                    $('#list_equivalencia').html(" ");
                }
            });

            $("#close_modal_create").click(function(e) {
                $("#CreateCursoChangeEquivalence").modal('hide');
            });

            btnAdd.click(function(e) {
                let select1 = $('#id_curso_1');
                let select2 = $('#id_curso_2');
                if (select1.val() != null && select2.val() != null && !array1.includes(select1.val()) && !array2
                    .includes(select2.val())) {
                    array1.push(select1.val());
                    array2.push(select2.val());
                    let obj = {
                        id_change_course: btnAdd.val(),
                        first_discipline_course: select1.val(),
                        second_discipline_course: select2.val()
                    };
                    array_ids.push(obj);
                    let nome1 = "";
                    let nome2 = "";
                    select1.children().each(function(index) {
                        if (this.value == select1.val()) nome1 = this.innerHTML;
                    });
                    select2.children().each(function(index) {
                        if (this.value == select2.val()) nome2 = this.innerHTML;
                    });
                    let html = "<li class='list-group-item p-1' id='list-" + array_ids.length + "'>" +
                        "<span class='cursor-pointer del-item' size='" + array_ids.length +
                        "' first-disc-course='" + obj.first_discipline_course + "' second-disc-course='" + obj
                        .second_discipline_course + "'>" +
                        "<i class='fas fa-times-square text-danger mr-2'></i>" +
                        "</span>" +
                        "<span class='h3 min-w'>" + nome1 + "</span>" +
                        "<span class='text-success ml-2 mr-2'> = </span>" +
                        "<span class='h3 min-w'>" + nome2 + "</span>" +
                        "</li>";
                    listEquivalencia.append(html);
                    if (array_ids.length > 0)
                        eliminator();
                } else {
                    if (select1.val() != null && array1.includes(select1.val()))
                        alert("valor da primeira caixa de seleção já foi escolhido");
                    if (select2.val() != null && array2.includes(select2.val()))
                        alert("valor da segunda caixa de seleção já foi escolhido");
                }
            });

            function eliminator() {
                $('.del-item').click(function(e) {
                    let obj = $(this);
                    let item = $('#list-' + obj.attr('size'));
                    array1 = array1.filter(function(e) {
                        return e != obj.attr('first-disc-course');
                    });
                    array2 = array2.filter(function(e) {
                        return e != obj.attr('second-disc-course');
                    });
                    array_ids = array_ids.filter(function(e) {
                        return e.first_discipline_course != obj.attr('first-disc-course') && e
                            .second_discipline_course != obj.attr('second-disc-course');
                    })
                    item.remove();
                });
            }

            $("#lective_years").change(function() {
                $('#change-curso-table').DataTable().clear().destroy();
                Tabela();
            });

            $("#GeralTip").hide();
            $("#DIVcourse").hide();
            $("#DIVRole").hide();

            $("#userTipSelect").change(function() {
                let select = $("#userTipSelect").val();
                
                if (select == 1) {
                    $("#TituloCreatechange").text("IMPORTAR DADOS - ESTUDANTES");
                    $("#GeralTip").show();
                    $("#DIVcourse").show();
                    $("#DIVRole").hide();
                
                    $("#courseImportusers").val("");
                    $("#rolesImportusers").val("");
                    
                } else if (select == 2) {
                    $("#TituloCreatechange").text("IMPORTAR DADOS - DOCENTE OU STAFF´S");
                    $("#GeralTip").show();
                    $("#DIVRole").show();
                    $("#DIVcourse").hide();
                   
                    
                    $("#courseImportusers").val("");
                    $("#rolesImportusers").val("");
                    
                } else {
                    $("#GeralTip").hide();
                    $("#DIVcourse").hide();
                    $("#DIVRole").hide();
                    
                  
                    $("#courseImportusers").val("");
                    $("#rolesImportusers").val("");
                }

            });




            function Tabela() {
              

                let AnoDataTable = $('#change-curso-table').DataTable({
                    ajax: {
                        url: "import-user-ajax/",
                        "type": "GET"
                    },
                     buttons: ['colvis', 'excel', {
                            text: '<i class="fas fa-cloud-upload"></i> Importar',
                            className: 'btn-primary main ml-1 rounded btn-main new_change_course',
                            action: function(e, dt, node, config) {
                                let text_year = $("#lective_years").val();
                                $("#TituloCreatechange").text("IMPORTAR DADOS");
                                $("#InputYear").val(text_year);
                                $("input").text('');
                                $("#CreateCursoChange").modal('show');
                            }
                        }, {
                            text: '<i class="fas fa-cloud-download"></i> Exportar',
                            className: 'btn-dark main ml-1 rounded btn-main new_change_course',
                            action: function(e, dt, node, config) {
                                let text_year = $("#lective_years").val();
                                $("#TituloCreateExport").text("EXPORTAR DADOS");
                                $("#InputYear").val(text_year);
                                $("input").text('');
                                $("#CreateExportData").modal('show');
                            }
                        }



                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false

                        }, {
                            data: 'nome_usuario',
                            name: 'u_p.value',
                            searchable: true
                        }, 
                        {
                            data: 'email',
                            name: 'u0.email',
                            searchable: true
                        },
                        {
                            data: 'bilhete',
                            name: 'up_bi.value',
                            searchable: true
                        },
                        {
                            data: 'matricula',
                            name: 'up_meca.value',
                            visible: true
                        }, 
                        // {
                        //     data: 'matricula_antiga',
                        //     name: 'importUSer.codigo_old',
                        //     visible: true
                        // }, 
                        {
                            data: 'curso',
                            name: 'ct.display_name',
                            visible: true
                        },
                         {
                            data: 'curricular',
                            name: ' importUSer.ano_curricular',
                            visible: false,
                            searchable: true
                        },  
                        //  {
                        //     data: 'roles',
                        //     name: ' role',
                        //     visible: false,
                        //     searchable: false
                        // },  
                        {
                            data: 'criado_por',
                            name: 'u1.name',
                            visible: false,
                            searchable: false
                        },{
                            data: 'criado_a',
                            name: 'importUSer.created_at',
                            visible: false
                        }, {
                            data: 'actualizado_a',
                            name: 'importUSer.update_at',
                            visible: false
                        }, {
                            data: 'actions',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }


                    ],

                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
                AnoDataTable.page('first').draw('page');
                Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            }
            $("#modal_create_save").hide();

            const input = document.getElementById('jsonFileforlearn');
            input.addEventListener('change', async function(event) {
                const file = event.target.files[0];
                // Verifica a extensão do arquivo
                const fileExtension = getFileExtension(file.name);
                try {

                    if (fileExtension === 'xlsx' || fileExtension === 'xls') {
                        // Se for um arquivo Excel
                        const jsonObjectExcel = await excelToJson(file);

                        UsersReady = [];
                        jsonObjectExcel.forEach((objeto, indice) => {
                            const users = Object.getOwnPropertyNames(objeto);
                            const usersForLearn = {};
                            //nome para email
                            for (const user of users) {
                                const dadoFormatado = user.trim().replace(/\s+/g, "").replace(
                                    /[^a-zA-Z0-9_]/g, "");
                                if (dadoFormatado == "Datadenascimento") {
                                    usersForLearn[dadoFormatado] = excelSerialToDate(objeto[user]);
                                } else {
                                    usersForLearn[dadoFormatado] = objeto[user];
                                }

                            }
                            UsersReady.push(usersForLearn)
                        });


                        $("#AlertaModa").removeClass('alert-warning');

                        $("#alertMessage").text("Dados do arquivo excel foram preparados com sucesso, " +
                            jsonObjectExcel.length + " Registos encontrados")
                        $("#AlertaModa").addClass('alert-success');
                        $("#modal_create_save").show();




                    } else if (fileExtension === 'json') {
                        const jsonObject = await readFileAsJSON(file);
                        $("#alertMessage").text("Dados do arquivo JSON foram preparados com sucesso, " +
                            jsonObject.length + " Registos encontrados")
                        $("#AlertaModa").addClass('alert-success');
                        $("#modal_create_save").show();
                       
                    } else {
                        console.log('Arquivo não suportado:', file.name);
                        alert(
                            "Selecione um arquivo com formato válido para importação de dados - forLEARN"
                            );

                        $("#modal_create_save").hide();
                    }
                } catch (error) {
                    console.error('Erro ao processar o arquivo JSON:', error);
                    $("#modal_create_save").hide();
                }
            });


            $("#modal_create_save").click(function(e) {
                importData(UsersReady)
            });

            $("#close_modal_create").click(function(e) {
                location.reload();
            });

            function readFileAsJSON(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        try {

                            const json = JSON.parse(event.target.result);
                            resolve(json);
                        } catch (error) {
                            reject(error);
                        }
                    };

                    reader.onerror = function(event) {
                        reject(event.target.error);
                    };

                    reader.readAsText(file);
                });
            }


            function excelToJson(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, {
                            type: 'array'
                        });
                        const sheetName = workbook.SheetNames[
                            0]; // Assumindo que estamos lendo apenas a primeira planilha

                        const sheet = workbook.Sheets[sheetName];
                        const jsonData = XLSX.utils.sheet_to_json(sheet);

                        resolve(jsonData);
                    };

                    reader.onerror = function(err) {
                        reject(err);
                    };

                    reader.readAsArrayBuffer(file);
                });
            }

            function getFileExtension(filename) {
                return filename.split('.').pop().toLowerCase();
            }

            function importData(data) {

                const course = $("#courseImportusers").val();
                const role =   $("#rolesImportusers").val();
                const curricular_year = $("#anoCurricular").val();

                let selectedType = $("#userTipSelect").val();
                if (selectedType == "") {alert("Seleciona um tipo de usuário "); return }
              
                $.ajax({
                    url: "{!! route('send-import-data') !!}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        users: data,
                        course,
                        role,
                        curricular_year,
                        selectedType
                    },
                    cache: false,
                    dataType: 'json',

                }).done(function(result) {
      
                    if(result['Error'].length>0){
                        alert(resulte['ImportType'].length+" registros não foram importados  - "+resulte['ImportType'])
                        setTimeout(() => {
                            location.reload();  
                        },3000);
                    }else{
                        alert(`Os dados dos ${result['ImportType']} foram importados com sucesso`);
                        setTimeout(() => {
                            location.reload();  
                        },3000);
                    }
                });

            }


            function excelSerialToDate(serial) {
                // A data base do Excel é 1 de janeiro de 1900
                const excelEpoch = new Date(1900, 0, 1);
                if (serial >= 60) {
                    serial -= 1;
                }
                excelEpoch.setDate(excelEpoch.getDate() + serial - 1);
                const year = excelEpoch.getFullYear();
                const month = String(excelEpoch.getMonth() + 1).padStart(2, '0');
                const day = String(excelEpoch.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

           



        })();
    </script>
@endsection
