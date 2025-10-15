<title>Avaliações | forLEARN® by GQS</title>
@php
$isStudent = auth()
->user()
->hasRole(['student']);
@endphp
@extends('layouts.generic_index_new')
@section('page-title', $isStudent ? 'AVALIAÇÕES' : 'LISTAS DE PAUTAS')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Avaliações</li>
@endsection
@section('styles-new')
@parent
<style>
    .red {
        background-color: red !important;
    }

    .texto-vermelho {
        color: red;
    }

    .texto-verde {
        color: green;
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

    .casa-inicio {}

    .div-anolectivo {
        width: 300px;
        padding-top: 16px;
        padding-right: 0px;
        margin-right: 15px;
    }
</style>
@endsection
@section('selects')
@if (!$isStudent)
<div class="mb-2">
    <label for="lective_years">Selecione o ano lectivo</label>
    <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
        <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
        @foreach ($lectiveYears as $lectiveYear)
        <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected==$lectiveYear->id) selected @endif>
            {{ $lectiveYear->currentTranslation->display_name }}
        </option>
        @endforeach
    </select>
</div>
@endif
@endsection
@section('body')
@if ($isStudent)
@else
@if (auth()->user()->id == 24)




@endif
<table id="matriculations-table" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Curso </th>
            <th>Ano </th>
            <th>Turma </th>
            <th>Código</th>
            <th>Disciplina</th>
            <th>PP1</th>
            <th>PP1 (2ª)</th>
            <th>PP2</th>
            <th>PP2 (2ª)</th>
            <th>OA</th>
            <th>MAC</th>
            <th>Exame escrito</th>
            <th>CF</th>
            <th>Exame oral</th>
            <th>Recurso</th>
            <th>TESP</th>
            <th>Especial</th>
            <th>Extraordinário</th>
            <th>Geral</th>
    
            <th>TFC</th>


        </tr>
    </thead>
</table>
@endif

<div style="padding-right: 10px;height:0px;" id="home_button">
    <a data-toggle="tooltip" data-placement="bottom" title="Voltar ao Início" target=""
        class="p-2 pr-3 pl-3 btn btn-sm id_Home element" href="/avaliations/panel_avaliation"
        style="background-color: #81ee77; color:rgb(10, 10, 10); height: 40px;"><i
            class="fa-solid fa-house-chimney"></i></a>
</div>
@endsection
@section('scripts-new')
@parent
<script>
    let lective_year = $('#lective_year').val();

    document.getElementById('home_button').style.visibility = 'hidden';

    function hidden() {
        setTimeout(() => {
            const tr = document.querySelectorAll('#matriculations-table tbody tr');
            tr.forEach(e => {
                try {
                    let value = e.children[3].innerHTML;
                    if (value == "a")
                        e.classList.add('d-none');
                } catch (error) {}
            });
        }, 1);
    }

    $('.page-link').click(function() {
        hidden();
    });

    $('.dropdown-item').click(function() {
        hidden();
    });

    $('.form-control.form-control-sm').keyup(function() {
        hidden();
    });

    $('.custom-select.custom-select-sm').change(function() {
        hidden();
    });

    $('#matriculations-table tbody').ready(function() {
        hidden();
    });

    function pauta_publicadas(lective_year) {
        // $('#matriculations-table').DataTable().clear().destroy();

        var AnoDataTable = $('#matriculations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "/avaliations/panel_avaliation_tabela/" + lective_year,
                "type": "GET"
            },
            buttons: [
                'colvis',
                'excel'
            ],
            columnDefs: [{
                targets: [3],
                render: function(data, type, row, meta) {

                    var turma = meta.settings.aoData[meta.row]._aData.nome_turma;
                    var disciplina = meta.settings.aoData[meta.row]._aData.codigo_disciplina;
                    var curso = meta.settings.aoData[meta.row]._aData.nome_curso;
                    var ano = meta.settings.aoData[meta.row]._aData.ano_curricular;

                    var cg_t = turma.slice(0, 3);
                    var cg_d = disciplina.slice(0, 3);

                    if ((curso == "Ciências Económicas e Empresariais") && (ano > 2)) {

                        if (turma.includes(cg_d)) {
                            return turma;
                        } else {
                            hidden();
                            return "a";
                        }


                    } else {

                        return turma;
                    }
                }
            }],
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nome_curso',
                    name: 'nome_curso',
                    searchable: true
                },
                {
                    data: 'ano_curricular',
                    name: 'ano_curricular',
                    searchable: true
                },
                {
                    data: 'nome_turma',
                    name: 'nome_turma',
                    searchable: true
                },
                {
                    data: 'codigo_disciplina',
                    name: 'codigo_disciplina',
                    searchable: true
                },
                {
                    data: 'nome_disciplina',
                    name: 'nome_disciplina',
                    searchable: true
                }, {
                    data: 'pf1',
                    name: 'pf1'
                }, {
                    data: 'pf1_2c',
                    name: 'pf1_2c'
                }, {
                    data: 'pf2',
                    name: 'pf2'
                }

                , {
                    data: 'pf2_2c',
                    name: 'pf2_2c'
                },
                {
                    data: 'oa',
                    name: 'oa'
                },
                {
                    data: 'mac',
                    name: 'mac'
                }, {
                    data: 'exame_escrito',
                    name: 'exame_escrito'
                },
                {
                    data: 'cf',
                    name: 'cf'
                }

                , {
                    data: 'exame_oral',
                    name: 'exame_oral',
                    visible: false
                }, {
                    data: 'recurso',
                    name: 'recurso'
                }, {
                    data: 'seminario',
                    name: 'seminario',
                    visible: false
                }, {
                    data: 'exame_especial',
                    name: 'exame_especial'
                },
                {
                    data: 'exame_extraordinario',
                    name: 'exame_extraordinario'
                }
                ,
                {
                    data: 'pauta_geral',
                    name: 'pauta_geral'
                },
                {
                    data: 'tfc',
                    name: 'tfc',
                    visible: false
                }

            ],
            pageLength: 10,
            "lengthMenu": [
                [10, 50, 100, 50000],
                [10, 50, 100, "Todos"]
            ],
            language: {
                url: '{{ asset('lang / datatables / ' . App::getLocale() . '.json ') }}'
                
            }
        });
    }

    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    // QUANDO A PAGINA FOR CARREGADA
    $(function() {
        lective_year = $('#lective_year').val();

        // console.log(lective_year);

        pauta_publicadas(lective_year);
    });

    // QUANDO O ANO LECTIVO FOR TROCADO
    $("#lective_year").change(function() {
        lective_year = $('#lective_year').val();

        // console.log(lective_year);

        $('#matriculations-table').DataTable().clear().destroy();
        pauta_publicadas(lective_year);
    });
</script>
@endsection