<script>
  $(document).ready(function(){
    var table = $('.data-table').DataTable({
       /* processing: true,
        serverSide: true,
        ajax: "{{ route('index') }}",
        columns: [
            //{data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'roles', name: 'roles'},
            {data: 'created_at', name:'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            //{data: 'action', name: 'action', orderable: false, searchable: false},
        ],*/
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
  rules: [{
   id: 'created_at',
    operator: 'equal',
    value: '1991/11/17'
  }, {
    condition: 'OR',
    rules: [{
      id: 'code',
      operator: 'equal',
    },
    ]
  }]
};
// Fix for Bootstrap Datepicker
$('#builder-widgets').on('afterUpdateRuleValue.queryBuilder', function(e, rule) {
  if (rule.filter.plugin === 'datepicker') {
    rule.$el.find('.rule-value-container input').datepicker('update');
  }
});

$('#builder-basic').queryBuilder({
  filters: [{
    id: 'roles_id',
    label: 'Cargo',
    type: 'integer',
    input: 'select',
    values: {
        1:  'Docente',
        8:  'Docente > Director Geral',
        9:  'Docente > Vice Director Área Académica',
        10: 'Docente > Vice Director Área Científica',
        11: 'Docente > Chefe Departamento',
        12: 'Docente > Coordenador Curso',
        13: 'Docente > Coordenador Unidade Curricular',
        14: 'Docente > Regente Unidade Curricular',
        2:  'Super Administrador',
        6:  'Estudante',
        15: 'Candidato a Estudante',
        7:  'Staff > Administrador',
        16: 'Staff > Director Executivo',
        17: 'Staff > Chefe Departamento',
        18: 'Staff > Chefe Secretaria',
        19: 'Staff > Chefe Secção',
        20: 'Staff > Tesoureiro',
        21: 'Staff > Auxiliar Administrativo',
        22: 'Staff > Secretário',
        23: 'Staff > Assistente Administrativo',
        24: 'Staff > Vigilante',
        25: 'Staff > Motorista',
        26: 'Staff > Auxiliar Higiente',
        27: 'Staff > Seguranca',
        29: 'Staff > Gestor Forlearn',
        41: 'Staff > Inscrições',
        42: 'Staff > Matrículas',
        43: 'Staff > Gabinete de termos',
        44: 'Staff > Recursos Humanos'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  }, {
    id: 'user_name',
    label: 'Nome',
    type: 'string'
  },

  {
    id: 'matricula_numb',
    label: 'Código de matrícula',
    type: 'string'
  },

  
  {
    id: 'code',
    label: 'Código da disciplina',
    type: 'string'
  },
  
/*
  {
    id: 'display_name',
    label: 'Turma do Estudante',
    type: 'integer',
    input: 'select',
    values: {
        'BIO1M1': 'BIO1M1 - Manhã',
        'BIO1T1': 'BIO1T1 - Tarde',
        'BIO1N1': 'BIO1N1 - Noite',
        'BIO2M1': 'BIO2M1 - Manhã',
        'BIO2T1': 'BIO2T1 - Tarde',
        'BIO2N1': 'BIO2N - Noite',
        'BIO3M1': 'BIO3M1 - Manhã',
        'BIO3T1': 'BIO3T1 - Tarde',
        'BIO4M1': 'BIO4M1 - Manhã',
        'BIO4N1': 'BIO4N1 - Noite',
        'DTO1M1': 'DTO1M1 - Manhã',
        'DTO1T1': 'DTO1T1 - Tarde',
        'DTO1N1': 'DTO1N1 - Noite',
        'DTO2M1': 'DTO2M1 - Manhã',
        'DTO2N1': 'DTO2N1 - Noite',
        'DTO2T1': 'DTO2T1 - Tarde',
        'DTO3M1': 'DTO3M1 - Manhã',
        'DTO3N1': 'DTO3N1 - Noite',
        'DTO3T1': 'DTO3T1 - Tarde',
        'DTO4M1': 'DTO4M1 - Manhã',
        'DTO4T1': 'DTO4T1 - Tarde',
        'DTO4N1': 'DTO4N1 - Noite',
        'DTO5T1': 'DTO5T1 - Tarde',
        'DTO5N1': 'DTO5N1 - Noite',
        'GEO1M1': 'GEO1M1 - Manhã',
        'GEO1T1': 'GEO1T1 - Tarde',
        'GEO2T1': 'GEO2T1 - Tarde',
        'GEO2N1': 'GEO2N1 - Noite',
        'GEO3T1': 'GEO3T1 - Tarde',
        'GEO4T1': 'GEO4T1 - Tarde',
        'HIS1N1': 'HIS1N1 - Noite',
        'HIS2M1': 'HIS2M1 - Manhã',
        'HIS2N1': 'HIS2N1 - Noite',
        'HIS3T1': 'HIS3T1 -  Tarde',
        'HIS4N1': 'HIS4N1 - Noite',
        'HIS2T1': 'HIS2T1 - Tarde',
        'PED1M1': 'PED1M1 - Manhã',
        'PED1T1': 'PED1T1 - Tarde',
        'PED1N1': 'PED1N1 - Noite',
        'PED2M1': 'PED2M1 - Manhã',
        'PED2T1': 'PED2T1 - Tarde',
        'PED2N1': 'PED2N1 - Noite',
        'PED3M1': 'PED3M1 - Manhã',
        'PED3T1': 'PED3T1 - Tarde',
        'PED4M1': 'PED4M1 - Manhã',
        'PED4T1': 'PED4T1 - Tarde',
        'PED4N1': 'PED4N1 - Noite',
        'PSG1M1': 'PSG1M1 - Manhã',
        'PSG1T1': 'PSG1T1 - Tarde',
        'PSG1N1': 'PSG1N1 - Noite',
        'PSG2M1': 'PSG2M1 - Manhã',
        'PSG2T1': 'PSG2T1 - Tarde',
        'PSG2N1': 'PSG2N1 - Noite',
        'PSG3M1': 'PSG3M1 - Manhã',
        'PSG3T1': 'PSG3T1 - Tarde',
        'PSG4M1': 'PSG4M1 - Manhã',
        'PSG4T1': 'PSG4T1 - Tarde',
        'PSG4N1': 'PSG4N1 - Noite',
        'SOC1M1': 'SOC1M1 - Manhã',
        'SOC1T1': 'SOC1T1 - Tarde',
        'SOC2M1': 'SOC2M1 - Manhã',
        'SOC2T1': 'SOC2T1 - Tarde',
        'SOC3M1': 'SOC3M1 - Manhã',
        'SOC3N1': 'SOC3N1 - Noite',
        'SOC4T1': 'SOC4T1 - Tarde',
        'DEF1M1': 'DEF1M1 - Manhã',
        'DEF2M1': 'DEF2M1 - Manhã',
        'DEF2T1': 'DEF2T1 - Tarde',
        'DEF3T1': 'DEF3T1 - Tarde',
        'DEF4T1': 'DEF4T1 - Tarde',
        'INF1T1': 'INF1T1 - Tarde',
        'INF2M1': 'INF2M1 - Manhã',
        'INF2N1': 'INF2N1 - Noite',
        'INF3M1': 'INF3M1 - Manhã',
        'INF3N1': 'INF3N1 - Noite',
        'INF4M1': 'INF4M1 - Manhã',
        'INF5N1': 'INF5N1 - Noite',
        'CEE1M1': 'CEE1M1 - Manhã',
        'CEE1T1': 'CEE1T1 - Tarde',
        'CEE1N1': 'CEE1N1 - Noite',
        'CEE2M1': 'CEE2M1 - Manhã',
        'CEE2N1': 'CEE2N1 - Noite',
        'GRH1M1': 'GRH1M1 - Noite',
        'GRH1T1': 'GRH1T1 - Tarde',
        'GRH1N1': 'GRH1N1 - Noite',
        'GRH2M1': 'GRH2M1 - Manhã',
        'GRH2N1': 'GRH2N1 - Noite',
        'GRH2T1': 'GRH2T1 - Tarde',
        'GRH3M1': 'GRH3M1 - Manhã',
        'GRH3T1': 'GRH3T1 - Tarde',
        'GRH4M1': 'GRH4M1 - Manhã',
        'GRH4T1': 'GRH4T1 - Tarde',
        'GRH4N1': 'GRH4N1 - Noite',
        'RI1M1': 'RI1M1 - Manhã',
        'RI1T1': 'RI1T1 - Tarde',
        'RI2M1': 'RI2M1 - Manhã',
        'RI3T1': 'RI3T1 - Tarde',
        'RI3N1': 'RI3N1 - Noite',
        'RI4M1': 'RI4M1 - Manhã',
        'RI4N1': 'RI4N1 - Noite',
        'COA4M1': 'COA4M1 -  Manhã',
        'COA4N1': 'COA4N1 - Noite',
        'COA3N1': 'COA3N1 - Noite',
        'PSJ2N1': 'PSJ2N1 - Noite',
        'PSJ3N1': 'PSJ3N1 - Noite',
        'PSJ4N1': 'PSJ4N1 - Noite',
        'PSJ5N1': 'PSJ5N1 - Noite',
        'GEE3M': 'GEE3M - Manhã',
        'GEE4N': 'GEE4N - Noite',
        'PSI1N1': 'PSI1N1 - Noite',
        'ECON3T1': 'ECON3T1 - Tarde',

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  */
  /*{
    id: 'created_att',
    label: 'Datepicker',
    type: 'date',
    validation: {
      format: 'YYYY/MM/DD'
    },
    plugin: 'datepicker',
    plugin_config: {
      format: 'yyyy/mm/dd',
      todayBtn: 'linked',
      todayHighlight: true,
      autoclose: true
    }
  },*/
  {
    id: 'created_at',
    label: 'Data ',
    type: 'datetime',
    input: 'text',
    validation: {
      
    },
    placeholder: "AA-MM-DD HH:MM",
    plugin: 'datetimepicker',
    plugin_config: {
     
    },
    operators: ['between','less', 'less_or_equal', 'greater', 'greater_or_equal']
  },
  /*
  {
    id: 'code',
    label: 'Nº de Candidato',
    type: 'string'
  },
  */
  {
    id: 'param_mecanografico',
    field: 'value_mecanografico',
    data: {
        parameter_id: 19
    },
    label: 'Nº Mecanografico',
    type: 'string'
    }, {
    id: 'param_sexo',
    field: 'value_sexo',
    data: {
    parameter_id: 2
    },
    label: 'Sexo',
    type: 'integer',
    input: 'select',
    values: {
        124: 'Masculino',
        125: 'Feminino'
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },{
    id: 'courses_id',
    label: 'Curso',
    type: 'integer',
    input: 'select',
    values: {
        25: 'Ciências Económicas e Empresariais',
        20: 'Direito',
        28: 'Engenharia Informática',
        11: 'Ensino de Biologia',
        18: 'Ensino de Psicologia',
        19: 'Educação Física e Desportos',
        21: 'Ensino de Sociologia',
        22: 'Ensino de Pedagogia',
        26: 'Ensino de Geografia',
        16: 'Ensino de História',
        27: 'Gestão de Recursos Humanos',
        15: 'Psicologia Jurídica',
        23: 'Relações Internacionais',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
  {
    id: 'ano_curricular',
    label: 'Ano curricular',
    type: 'integer',
    input: 'select',
    values: {
      1: '1º', 
      2: '2º',
      3: '3º',
      4: '4º',
      5: '5º'
    },
    operators: ['equal', 'not_equal']
  },
  /*
  {
    id: 'classes',
    label: 'Turma do Candidato',
    type: 'integer',
    input: 'select',
    values: {
        10: 'BIO1M1 - Biologia - Manhã',
        11: 'BIO1T1 - Biologia - Tarde',
        12: 'BIO1N1 - Biologia - Noite',
        13: 'DTO1M1 - Direito  - Manhã',
        14: 'DTO1T1 - Direito  - Tarde',
        15: 'DTO1N1 - Direito  - Noite',
        16: 'GEO1M1 - Geografia - Manhã',
        17: 'GEO1T1 - Geografia - Tarde',
        18: 'HIS1N1 - História  - Noite',
        19: 'PED1M1 - Pedagogia - Manhã',
        20: 'PED1T1 - Pedagogia - Tarde',
        21: 'PED1N1 - Pedagogia - Noite',
        22: 'PSG1M1 - Psicologia - Manhã',
        23: 'PSG1T1 - Psicologia - Tarde',
        24: 'PSG1N1 - Psicologia - Noite',
        25: 'SOC1M1 - Sociologia - Manhã',
        26: 'SOC1T1 - Sociologia - Tarde',
        27: 'DEF1M1 - Educação Fisíca e Desportos - Manhã',
        28: 'INF1T1 - Informática - Tarde',
        29: 'CEE1M1 - Ciências Económicas Empresariais - Manhã',
        30: 'CEE1T1 - Ciências Económicas Empresariais - Tarde',
        31: 'CEE1N1 - Ciências Económicas Empresariais - Noite',
        32: 'GRH1M1 - Gestão de Recursos Humanos - Noite',
        33: 'GRH1T1 - Gestão de Recursos Humanos - Tarde',
        34: 'GRH1N1 - Gestão de Recursos Humanos - Noite',
        35: 'PSI1N1 - Psicologia Jurídica - Noite',
        36: 'RI1M1  - Relações Internacionais - Manhã',
        37: 'RI1T1  - Relações Internacionais - Tarde',

    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },{
    id: 'param_civil',
    field: 'value_civil',
    data: {
        parameter_id: 4
    },
    label: 'Estado Civíl',
    type: 'integer',
    input: 'select',
    values: {
      97: 'Solteiro (a)',
      98: 'Casado (a)',
      99: 'Divorciado (a)',
      100: 'Viúvo (a)',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_empty', 'is_not_empty']
  },
  {
    id: 'param_email',
    field: 'value_email',
    data: {
        parameter_id: 34
    },
    label: 'Email Pessoal',
    type: 'string'
    },
    {
    id: 'param_sangue',
    field: 'value_sangue',
    data: {
        parameter_id: 32
    },
    label: 'Tipo de Sangue',
    type: 'integer',
    input: 'select',
    values: {
      175: 'A+',
      176: 'A-',
      177: 'B+',
      178: 'B-',
      179: 'AB+',
      180: 'AB-',
      181: 'O+',
      182: 'O-',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_empty', 'is_not_empty']
  },{
    id: 'param_nacionalidade',
    field: 'value_nacionalidade',
    data: {
        parameter_id: 6
    },
    label: 'Nacionalidade',
    type: 'integer',
    input: 'select',
    values: {
      2655: 'Angolano (a)',
      2656: 'Cubano (a)',
      2657: 'Brasileiro (a)',
      2658: 'Português (a)',
      2659: 'Cabo-verdiano (a)',
      2660: 'Guiné-Bissau',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_empty', 'is_not_empty']
  },
    //Parametros de Ensino

  //--Bacharelato
    {
    id: 'param_bacharelato',
    field: 'value_bacharelato',
    data: {
        parameter_id: 285
    },
    label: 'Bacharelato',
    type: 'integer',
    input: 'radio',
    values: {
      2432: 'Sim',
      2433: 'Não'
    },
    operators: ['equal']
  },

    //--Licenciatura--//
  {
    id: 'param_licenciatura',
    field: 'value_licenciatura',
    data: {
        parameter_id: 263
    },
    label: 'Licenciatura',
    type: 'integer',
    input: 'radio',
    values: {
      2626: 'Sim',
      2627: 'Não'
    },
    operators: ['equal']
  },

    //--Mestrado--//
    {
    id: 'param_mestrado',
    field: 'value_mestrado',
    data: {
        parameter_id: 286
    },
    label: 'Mestrado',
    type: 'integer',
    input: 'radio',
    values: {
      2444: 'Sim',
      2445: 'Não'
    },
    operators: ['equal']
  },

    //--Doutoramento--//
    {
    id: 'param_doutoramento',
    field: 'value_doutoramento',
    data: {
        parameter_id: 287
    },
    label: 'Doutoramento',
    type: 'integer',
    input: 'radio',
    values: {
      2624: 'Sim',
      2625: 'Não'
    },
    operators: ['equal']
  },

    //!-----Parametros de Ensino

    //Peso
    {
    id: 'param_peso',
    field: 'value_peso',
    data: {
            parameter_id: 31
        },
        label: 'Peso (kg)',
        type: 'integer'
    },
        //Provincia de Origem
     {
     id: 'param_value_provincia_origem',
     field: 'value_provincia_origem',
     data: {
         parameter_id: 150
     },
     label: 'Provincia de Origem',
     type: 'integer',
     input: 'select',
     values: {
       2357:'Bengo',
       2358: 'Benguela',
       2359:'Cabinda',
       2360:'Cunene',
       2361:'Cuando-Cuabngo',
       2362:'Cuanza-Norte',
       2363:'Cuanza-Sul',
       2364:'Huambo',
       2365:'Huíla',
       2366:'Luanda',
       2367:'Lunda-Norte',
       2368:'Lunda-Sul',
       2369:'Malange',
       2370:'Moxico',
       2371:'Namibe',
       2372:'Uíge',
       2373:'Zaire'
     },
     operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
   },//Provincia Actual
     {
     id: 'param_value_provincia_actual',
     field: 'value_provincia_actual',
     data: {
         parameter_id: 150
     },
     label: 'Provincia Actual',
     type: 'integer',
     input: 'select',
     values: {
       2357:'Bengo',
       2358: 'Benguela',
       2359:'Cabinda',
       2360:'Cunene',
       2361:'Cuando-Cuabngo',
       2362:'Cuanza-Norte',
       2363:'Cuanza-Sul',
       2364:'Huambo',
       2365:'Huíla',
       2366:'Luanda',
       2367:'Lunda-Norte',
       2368:'Lunda-Sul',
       2369:'Malange',
       2370:'Moxico',
       2371:'Namibe',
       2372:'Uíge',
       2373:'Zaire'
     },
     operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
   },
  {
    id: 'param_nascimento',
    field: 'value_nascimento',
    data: {
        parameter_id: 5
    },
    label: 'Data de Nascimento',
    type: 'string',
    operators: ['equal','contains','between','not_equal','less','less_or_equal','greater','greater_or_equal','not_between','in', 'not_in','is_empty','is_not_empty']
  },
  */
//   {
//     id: 'in_stock',
//     label: 'In stock',
//     type: 'integer',
//     input: 'radio',
//     values: {
//       1: 'Yes',
//       0: 'No'
//     },
//     operators: ['equal']
//   },
  /*{
    id: 'users.id',
    label: 'COD. UTILIZADOR',
    type: 'integer',
  },*/

  /*
  {
    id: 'param_altura',
    field: 'value_altura',
    data: {
        parameter_id: 30
    },
    label: 'Altura',
    type: 'double',
    validation: {
    min: 0,
    step: 0.01
  }
},
    //Trabalhador e estudante
    {
    id: 'param_trabalhador_estudante',
    field: 'value_trabalhador_estudante',
     data: {
            parameter_id: 62
        },
    label: 'Trabalhador Estudante',
    type: 'integer',
    input: 'checkbox',
    values: {
      213: 'Sim',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },



  //Necessidades Especiais
    {
    id: 'param_nec_especiais',
    field: 'value_nec_especiais',
     data: {
            parameter_id: 289
        },
    label: 'Necessidades Especiais',
    type: 'integer',
    input: 'checkbox',
    values: {
      2568: 'Audição',
      2569: 'Visual',
      2570: 'Motora',
    },
    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
  },
*/

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
        url: "{{ route('getResults') }}",
        type: "POST",
        data: (JSON.stringify(result)),
        processData:true,
        contentType: 'application/json; charset=utf-8',
        success: function (response)
        {
          $('#exampleModalCenter').modal('hide');
        },
        error: function(xhr, status, error){
        $('#exampleModalCenter').modal('hide');
        alert("Erro!" + xhr.status);
    },
    }).done(
        function(data)
        {
            
           //table.draw();
             $('#group').hide(); //Esconder a tabela principal antes de chamar a dos resultados
             $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
        }
    )

});
</script>