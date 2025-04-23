<script>
  $(document).ready(function(){
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('courses.reports-index') }}",
        columns: [
            //{data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'code', name: 'code'},
            {data: 'display_name', name: 'ct.display_name'},
            {data: 'duration', name: 'duration_value'},
            {data: 'created_by', name: 'u1.name'},
            {data: 'updated_by', name:'u2.name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            //{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        //Configuração do plugins dos botões para (impimir, copiar, pdf, e excel)
        dom: 'Bfrtip',
        "order": [[ 1, 'asc' ]],
          "language":{
              "lengthMenu": "Exibir _MENU_ records per page",
          },
        buttons: [
            {
                 extend: 'pageLength',

             },
             {
                 extend: 'colvis',
                 text: 'Colunas Visíveis <i class="fas fa-sort-down"></i>',
                 stateSave: true,
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
                },
                customize: function(win){
                    $(win.document.body)
                    .css('font-size', '10pt')
                    .prepend(
                            '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'
                        );;
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
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
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
  });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

     var rules_basic = {
  condition: 'AND',
  rules: [

   {
    condition: 'OR',
    rules: [{
      id: 'code',
      operator: 'equal',
    },
    ]
  }]
};

$('#builder-basic').queryBuilder({

  filters: [
   {
    id: 'duration_type',
    label: 'Tipo de Duração',
    type: 'integer',
    input: 'select',
    values: {
        6:  'Anos',
        7:  'Semestres',
        12: 'Semanas',
        8:  'Meses'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  }, {
    id: 'duration_value',
    label: 'Duração',
    type: 'integer'
  },
   {
    id: 'course_cycles',
    label: 'Ciclo de Curso',
    type: 'integer',
    input: 'select',
    values: {
        19:  '1º Ciclo',
        20:  '2º Ciclo'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  {
    id: 'degrees',
    label: 'Grau de Ensino',
    type: 'integer',
    input: 'select',
    values: {
        15:  'Bacharelato',
        16:  'Licenciatura',
        17:  'Mestrado',
        18:  'Doutoramento'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  {
    id: 'departments',
    label: 'Departamento',
    type: 'integer',
    input: 'select',
    values: {
        11:  'Departamento Ciências da Educação',
        12:  'Departamento Ciências Económicas e Jurídicas',
        13:  'Departamneto Ciência e Tecnologia'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
],

  
});
$('#exampleModalCenter').modal('hide');
$('#btn-get').on('click', function() {
  var result = $('#builder-basic').queryBuilder('getRules');
  $('#exampleModalCenter').modal('show');


  //if (!$.isEmptyObject(result)) {
    //alert(JSON.stringify(result, null, 2));
  //}
  //console.log(result);

    $.ajax({
        url: "{{ route('couses.getCoursesResults') }}",
        type: "POST",
        data: (JSON.stringify(result)),
        processData:true,
        contentType: 'application/json; charset=utf-8',
        success: function (response)
        {

        }
    }).done(
        function(data)
        {
            $('#exampleModalCenter').modal('hide');
           //table.draw();
             $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
             $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
        }
    )

});
</script>
