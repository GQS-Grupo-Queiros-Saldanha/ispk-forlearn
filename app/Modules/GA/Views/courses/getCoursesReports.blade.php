<table class="table table-striped table-hover data-tables">
            <thead>
                <tr>
                    <th>Nº de Ordem</th> 
                    <th>Nome </th>
                    <th>Tipo de Duração</th>
                    <th>Duração</th>
                    <th>Ciclo de Curso</th>
                    <th>Grau de Ensino</th>
                    <th>Departamento</th>
                    <th>Criado Por</th>
                    <th>Atualizado Por</th>
                   
                </tr>
            </thead>
            <tbody>￼
                @foreach ($rows as $item)
                        <tr>
                        <td></td>
                        <td>{{ $item->course_name }} </td>
                        <td> {{ $item->duration_type_name}} </td>
                        <td>{{ $item->duration_value }}</td>
                        <td>{{ $item->course_cycles_name }}</td>
                        <td>{{ $item->degree_name }}</td>
                        <td>{{ $item->departments_name }}</td>
                        <td>{{ $item->created_by}}</td>
                        <td>{{ $item->updated_by}}</td>
                    </tr>￼
                @endforeach
            </tbody>
        </table>
<script>
    $(document).ready(function(){
    var table = $('.data-tables').DataTable({

         "columnDefs": [
             {
                 "targets": [3],
                 "visible": true,
             },
          
             
            
         ],
        "lengthMenu": [ [100, 25, 50, -1], [100, 25, 50, "Todos"] ],
        //Configuração do plugins dos botões para (impimir,￼ copiar, pdf, e excel)
        dom: 'Bfrtip',
           "order": [[ 1, 'asc' ]],
        buttons: [
             {
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
     table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
           cell.innerHTML = i+1;
           table.cell(cell).invalidate('dom');
     });
}).draw();
});
</script>

