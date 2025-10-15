<table class="table table-striped table-hover data-tables">
            <thead>
                <tr>
                    <th>Nº de Ordem</th>
                    <th>ID</th>
                    <th>Código da Disciplina</th>
                    <th>Nome da Disciplina</th>
                    <th>Descrição</th>
                    <th>Abreviação</th>
                    <th>Curso</th>
                    <th>Perfil da Disciplina</th>
                    <th>Área</th>
                    <th>Criado por</th>
                    <th>Actualizado por</th>
                    <th>Criado a</th>
                    <th>Actualizado a</th>
                </tr>
            </thead>
            <tbody>￼
                @foreach ($rows as $item)
                    <tr>
                        <td></td>
                        <td>{{ $item->discipline_id}}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->display_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->abbreviation }}</td>


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
                        <td>Licenciatura em Ciências Económicas e Emprariais</td>
                        @elseif($item->courses_id == 26)
                        <td>Licenciatura em Ensino de Geografia</td>
                        @elseif($item->courses_id == 27)
                        <td>Licenciatura em Gestão de Recursos Humanos</td>
                        @elseif($item->courses_id == 28)
                        <td>Licenciatura em Engenharia informática</td>
                         @else
                        <td> </td>
                            @endif

                         @if($item->discipline_profiles_id == 1)
                         <td>Normal</td>
                        @elseif($item->discipline_profiles_id == 2)
                        <td>Estágio</td> 
                         @elseif($item->discipline_profiles_id == 3)
                        <td>Dissertação</td>   
                        @elseif($item->discipline_profiles_id == 4)
                        <td>Opcional</td> 
                        @elseif($item->discipline_profiles_id == 8)
                        <td>Exame de Admissão</td> 
                        @else
                        <td> </td>
                        @endif

                    
                        @if($item->discipline_area_id == 4)
                        <td>Matemática</td>
                        @elseif($item->discipline_area_id == 5)
                        <td>Programação</td>
                          @elseif($item->discipline_area_id == 6)
                        <td>Gestão</td>
                          @elseif($item->discipline_area_id == 7)
                        <td>Electónica</td>
                          @elseif($item->discipline_area_id == 9)
                        <td>Multimedia</td>
                          @elseif($item->discipline_area_id == 10)
                        <td>Ciência e Educação</td>
                          @elseif($item->discipline_area_id == 13)
                        <td>Geral</td>
                          @elseif($item->discipline_area_id == 14)
                        <td>Específica</td>
                          @elseif($item->discipline_area_id == 15)
                        <td>Profissional</td>
                          @elseif($item->discipline_area_id == 16)
                        <td>Línguas</td>
                          @elseif($item->discipline_area_id == 17)
                        <td>Jurídicas</td>
                          @elseif($item->discipline_area_id == 18)
                        <td>Exame de Admissão</td>
                         @else
                        <td> </td>
                        @endif

                        <td>{{ $item->created_by }}</td>
                        <td>{{ $item->updated_by }}</td>
                        <td>{{ $item->created_at }}</td>
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
                 "targets": [4],
                 "visible": false,
             },

             {
                 "targets": [7],
                 "visible": false,
             },

              {
                 "targets": [9],
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
