<table class="table table-striped table-hover data-tables">
            <thead>
                <tr>
                    <th>Nº de Ordem</th>
                    <th>Nº Mecanográfico</th>
                    <th>Estudante</th>
                    <th>E-mail</th>
                    <th>Curso</th>
                    <th>Ano Curricular</th>
                    <th>Turma do Estudante</th>
                    <th>Turma do Candidato</th>
                    <th>Disciplina</th>
                    <th>Nota</th>
                    <th>Criado por</th>
                    <th>Criado a</th>
                    <th>Actualizado por</th>
                    <th>Actualizado a</th>
                    </tr>
            </thead>
            <tbody>￼
                @foreach ($rows as $item)
                    <tr>
                        <td></td>
                        <td>{{ $item->param_mecanografico }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>

                        @if($item->courses_id == 11)
                        <td>Licenciatura em Ensino de Biologia</td>
                        @elseif($item->courses_id == 15)
                        <td>Licenciatura em Psicologia (Psicologia Jurídica)</td>
                        @elseif($item->courses_id == 16)
                        <td>Licenciatura em Ensino de história</td>
                        @elseif($item->courses_id == 18)
                        <td>Licenciatura em Ensino de Psicologia</td>
                        @elseif($item->courses_id == 19)
                        <td>Licenciatura em Educação Fisíca e Desportos</td>
                        @elseif($item->courses_id == 20)
                        <td>Licenciatura em Direito</td>
                        @elseif($item->courses_id == 21)
                        <td>Licenciatura em Ensino de Sociologia</td>
                        @elseif($item->courses_id == 22)
                        <td>Licenciatura em Ensino de Pedagogia</td>
                        @elseif($item->courses_id == 23)
                        <td>Licenciatura em Relações Internacionais</td>
                        @elseif($item->courses_id == 25)
                        <td>Licenciatura em Ciências Económicas e Empresariais</td>
                        @elseif($item->courses_id == 26)
                        <td>Licenciatura em Ensino de Geografia</td>
                        @elseif($item->courses_id == 27)
                        <td>Licenciatura em Gestão de Recursos Humanos</td>
                        @elseif($item->courses_id == 28)
                        <td>Licenciatura em Engenharia informática</td>
                         @else
                        <td> </td>
                            @endif
                        <td>{{ $item->ano }}</td>    
                        <td>{{ $item->turma_matriculado }}</td>
                        <td>{{ $item->turma_candidato }}</td>
                        <td>{{ $item->disciplina }}</td>
                        <td>{{ $item->value }}</td>
                        <td>{{ $item->created_by }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ $item->updated_at }}</td>
                    </tr>￼
                @endforeach
            </tbody>
        </table>
<script>
    $(document).ready(function(){
    var table = $('.data-tables').DataTable({
         "columnDefs": [
           {
                 "targets": [1],
                 "visible": false,
             },
             
             {
                 "targets": [3],
                 "visible": false,
             },

             {
                 "targets": [6],
                 "visible": false,
             },

             {
                 "targets": [7],
                 "visible": false,
             },

             {
                 "targets": [10],
                 "visible": false,
             },
             
             {
                 "targets": [11],
                 "visible": false,
             },

             {
                 "targets": [12],
                 "visible": false,
             },

             {
                 "targets": [13],
                 "visible": false,
             }
            
         ],
         "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "Todos"] ],
        //Configuração do plugins dos botões para (impimir,￼ copiar, pdf, e excel)
        dom: 'Bfrtip',
        buttons: [
             {
                 extend: 'pageLength',
                 text: 'Mostrar Registos'
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
     table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
           cell.innerHTML = i+1;
           table.cell(cell).invalidate('dom');
     });
}).draw();
    });
</script>
