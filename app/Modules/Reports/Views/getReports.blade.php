<table class="table table-striped table-hover data-tables">
    <thead>
        <tr>
            <th>Nº de Ordem</th>
            <th>ID do Utilizador</th>
            <th>Nº Mecanografico </th>
            <th>Nº Candidato </th>
            <th>Nome completo</th>
            <th>Nome de perfil</th>
            <th>E-mail</th>
            <th>Cargo</th>
            <th>Data de Nascimento</th>
            <th>Estado Civil</th>
            <th>Sexo</th>
            <th>Curso</th>
            <th>Turma do Candidato</th>
            <th>Turma do Estudante</th>
            <th>Altura</th>
            <th>Tipo de Sangue</th>
            <th>Nº Bilhete Identidade</th>
            <th>E-mail Pessoal</th>
            <th>Nacionalidade</th>
            <th>Bacharelato</th>
            <th>Licenciatura</th>
            <th>Mestrado</th>
            <th>Doutoramento</th>
            <th>Peso (kg)</th>
            <th>Data de Validade do Bilhete de Identidade</th>
            <th>Nº de Passaporte</th>
            <th>Data de Validade do Passaporte</th>
            <th>NIF</th>
            <th>Nº de Segurança Social</th>
            <th>Nº Atestado Médico</th>
            <th>Nº do Registro Criminal</th>
            <th>Nº do Ressenciamento Militar</th>
            <th>Nº da Carta de Condução</th>
            <th>Estudante Trabalhador</th>
            <th>Necessidades Especiais</th>
            <th>Data do Termo de Trabalho</th>
            <th>Nº IBAN</th>
            <th>Telefone Fixo</th>
            <th>Telefone Principal</th>
            <th>Telefone Alternativo</th>
            <th>Email Alternativo</th>
            <th>Whatsapp</th>
            <th>Skype</th>
            <th>Facebook</th>
            <th>Provincia de Origem</th>
            <th>Provinctia Actual</th>
            <th>Atualizado Por </th>
            <th>Criado Por</th>
            {{--<th>Código da Disciplina </th>--}}
            <th>Nº de Matrícula</th>
            <th>Ano Curricular</th>
            <th>Fotografia</th>
            <th>Matrícula criada por</th>
            <th>Matrícula criada a</th>
            <th>Matrícula actualizada a</th>
            <th>Candidato criado a</th>
        </tr>
    </thead>
    <tbody>￼
        <?php $id = ''; ?>
        @foreach ($rows as $item)

        @if($item->value_mecanografico != $id)

        <tr>
            <td></td>
            <td>{{ $item->user_id}}</td>
            <td>{{ $item->value_mecanografico }} </td>
            <td> {{ $item->code_candidate}} </td>
            <td>{{ $item->value_nome }}</td>
            <td>{{ $item->user_name}}</td>
            <td>{{ $item->user_email }}</td>
            <td>{{ $item->role_name }}</td>
            <td>{{ $item->value_nascimento }}</td>
            <td>{{ $item->getcode_civil }}</td>
            <td>{{ $item->getcode_sexo}} </td>
            <td> {{ $item->course_name}}</td>
            <td> {{ $item->turma_display_name}}</td>
            <td>{{ $item->turma_matriculado}}</td>
            <td>{{ $item->value_altura }}</td>
            <td>{{ $item->getcode_sangue }}</td>
            <td>{{ $item->value_bilhete }}</td>
            <td>{{ $item->value_email }}</td>
            <td>{{ $item->getcode_nacionalidade }}</td>
            <td>{{ (substr($item->getcode_bacharelato, -3) == "sim") ? 'Sim':'Não' }}</td>
            <td>{{ (substr($item->getcode_licenciatura, -3) == "sim") ? 'Sim':'Não' }}</td>
            <td>{{ (substr($item->getcode_mestrado, -3) == "sim") ? 'Sim':'Não' }}</td>
            <td>{{ (substr($item->getcode_doutoramento, -3) == "sim") ? 'Sim':'Não' }}</td>
            <td>{{ $item->value_peso }}</td>
            <td>{{ $item->value_validade_bilhete }}</td>
            <td>{{ $item->value_passaporte }}</td>
            <td>{{ $item->value_validade_passaporte }}</td>
            <td>{{ $item->value_nif }}</td>
            <td>{{ $item->value_segsocial }}</td>
            <td>{{ $item->value_atestmedico }}</td>
            <td>{{ $item->value_regcriminal }}</td>
            <td>{{ $item->value_ressmilitar }}</td>
            <td>{{ $item->value_cartaconducao }}</td>
            <td>{{ $item->getcode_estudantetrabalhador }}</td>
            <td>{{ $item->getcode_necespeciais }}</td>
            <td>{{ $item->value_data_termo_trabalho }}</td>
            <td>{{ $item->value_iban }}</td>
            <td>{{ $item->value_teleffixo }}</td>
            <td>{{ $item->value_telefprincipal }}</td>
            <td>{{ $item->value_telefalternativo }}</td>
            <td>{{ $item->value_emailalternativo }}</td>
            <td>{{ $item->value_whatsapp }}</td>
            <td>{{ $item->value_skype }}</td>
            <td>{{ $item->value_facebook }}</td>
            <td>{{ (substr($item->getcode_value_provincia_origem, -1) == "x") ? substr($item->getcode_value_provincia_origem, 0, -1) : $item->getcode_value_provincia_origem }}
            </td>
            <td>{{ (substr($item->getcode_value_provincia_actual, -1) == "x") ? substr($item->getcode_value_provincia_actual, 0, -1) : $item->getcode_value_provincia_actual }}
            </td>
            <td>{{ $item->updated_by}}</td>
            <td>{{ $item->created_by}}</td>
            {{--<td>{{ $item->d_c}}</td>--}}
            <td>{{ $item->matricula_numb}}</td>
            <td>{{ $item->ano_curricular}}</td>
            <td>{{ $item->value_fotografia}}</td>
            <td>{{ $item->matriculations_created_by}}</td>
            <td>{{ $item->matriculations_created_at}}</td>
            <td>{{ $item->matriculations_updated_at}}</td>
            <td>{{ $item->user_candidate_created_at}}</td>
        </tr>￼
        <?php $id = $item->value_mecanografico; ?>
        @endif

        @endforeach

    </tbody>
</table>
<script>
    $(document).ready(function () {
        var table = $('.data-tables').DataTable({

            "columnDefs": [{
                    "targets": [1],
                    "visible": false,
                },

                {
                    "targets": [5],
                    "visible": false,
                },
                {
                    "targets": [7],
                    "visible": false,
                },
                {
                    "targets": [8],
                    "visible": false,
                },
                {
                    "targets": [9],
                    "visible": false,
                },
                {
                    "targets": [13],
                    "visible": false,
                },
                {
                    "targets": [14],
                    "visible": false,
                },
                {
                    "targets": [15],
                    "visible": false,
                },
                {
                    "targets": [16],
                    "visible": false,
                },
                {
                    "targets": [17],
                    "visible": false,
                },
                {
                    "targets": [18],
                    "visible": false,
                },
                {
                    "targets": [19],
                    "visible": false,
                },
                {
                    "targets": [20],
                    "visible": false,
                },
                {
                    "targets": [21],
                    "visible": false,
                },
                {
                    "targets": [22],
                    "visible": false,
                },
                {
                    "targets": [23],
                    "visible": false,
                },
                {
                    "targets": [24],
                    "visible": false,
                },
                {
                    "targets": [25],
                    "visible": false,
                },
                {
                    "targets": [26],
                    "visible": false,
                },
                {
                    "targets": [27],
                    "visible": false,
                },
                {
                    "targets": [28],
                    "visible": false,
                },
                {
                    "targets": [29],
                    "visible": false,
                },
                {
                    "targets": [30],
                    "visible": false,
                },
                {
                    "targets": [31],
                    "visible": false,
                },
                {
                    "targets": [32],
                    "visible": false,
                },
                {
                    "targets": [33],
                    "visible": false,
                },
                {
                    "targets": [34],
                    "visible": false,
                },
                {
                    "targets": [35],
                    "visible": false,
                },
                {
                    "targets": [36],
                    "visible": false,
                },

                {
                    "targets": [37],
                    "visible": false,
                },
                {
                    "targets": [38],
                    "visible": false,
                },
                {
                    "targets": [39],
                    "visible": false,
                },
                {
                    "targets": [40],
                    "visible": false,
                },
                {
                    "targets": [41],
                    "visible": false,
                },
                {
                    "targets": [42],
                    "visible": false,
                },
                {
                    "targets": [43],
                    "visible": false,
                },
                {
                    "targets": [44],
                    "visible": false,
                },
                {
                    "targets": [45],
                    "visible": false,
                },
                {
                    "targets": [46],
                    "visible": false,
                },
                {
                    "targets": [47],
                    "visible": false,
                },
                {
                    "targets": [48],
                    "visible": false,
                },
                {
                    "targets": [49],
                    "visible": false,
                },
                {
                    "targets": [50],
                    "visible": false,
                },
                {
                    "targets": [51],
                    "visible": false,
                },
                {
                    "targets": [52],
                    "visible": false,
                },
                {
                    "targets": [53],
                    "visible": false,
                },
                {
                    "targets": [54],
                    "visible": false,
                }

            ],
            "lengthMenu": [
                [100, 25, 50, -1],
                [100, 25, 50, "Todos"]
            ],
            //Configuração do plugins dos botões para (impimir,￼ copiar, pdf, e excel)
            dom: 'Bfrtip',
            "order": [
                [1, 'asc']
            ],
            buttons: [{
                    extend: 'pageLength',
                    text: 'Mostrar Registros'
                },
                {
                    extend: 'colvis',
                    text: 'Colunas Visíveis <i class="fas fa-sort-down"></i>',
                    collectionLayout: 'fixed four-column'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-lg fa-file-pdf"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-lg fa-print"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-lg fa-copy"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
            ]
        });
        table.on('order.dt search.dt', function () {
            table.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
                table.cell(cell).invalidate('dom');
            });
        }).draw();
    });

</script>