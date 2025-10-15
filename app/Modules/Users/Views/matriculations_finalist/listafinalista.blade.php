@extends('layouts.generic_index_new')
@section('navbar')
@endsection
@section('page-title', 'Finalistas matriculados')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Finalista</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="">
        <label>Selecione o curso</label>
        <select name="curso" id="curso" class="selectpicker form-control form-control-sm"
            style="width: 100%!important;">
            <option selected value="">Seleciona o curso</option>
        </select>
    </div>
@endsection
@section('body')
    <table id="matriculationsFinalist-table" class="table table-striped table-hover">
        <thead>
            <tr>
                {{-- <th>Nº de Ordem</th> --}}
                <th id="dado">#</th>
                <th>Confirmação </th>
                <th>Matrícula</th>
                <th>Nome do estudante</th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Ano </th>
                <th>nº BI </th>
                {{-- <th>Estado do pagamento</th> --}}
                <th>@lang('common.created_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Estado do pagamento</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
    <div hidden>
        <a href="{{route('anulate.matriculation_finalist.index')}}" id="anulate-matriculation"></a>
        <a href="{{route('new.confirmation',"PARM")}}" id="new-matriculation"></a>
    </div>
@endsection
@section('models')
    @include('Users::anulate_matriculation.datatables.anulate')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            let curso = $("#curso");
            let anoLective = $("#lective_years");
            let tableFinalista = $('#matriculationsFinalist-table');
            let redirectCreate = $('#new-matriculation');
            const PIVO_PARM="PARM";
            let redirect="";

            getCurso();
            defineAttribuite();
            getQueryAjax(`ajaxListaFinalista/${anoLective.val()}`);

            curso.change((e) => {
                getQueryAjax(`ajaxListaFinalista/${anoLective.val()}?cursoBy=${curso.val()}`);
            })

            anoLective.change((e) => {
                defineAttribuite();
                getQueryAjax(`ajaxListaFinalista_forYear/${anoLective.val()}?cursoBy=${curso.val()}`);
            });

            function defineAttribuite() {
                let url = redirectCreate.attr('href');
                let ano = anoLective.val();
                if(ano != ""){
                    if(url.includes(PIVO_PARM)) redirect = url.replace(PIVO_PARM, ano);
                }else{
                    redirect = "";
                }
            }

            function getCurso() {
                $.ajax({
                    url: "getCurso",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    curso.empty();
                    curso.append('<option selected="" value="0">Selecione o curso</option>');
                    if (data['data'].length > 0) {
                        $.each(data['data'], function(indexInArray, row) {
                            curso.append('<option value="' + row.id + '">' + row.nome_curso +
                                '</option>');
                        });
                    }
                    curso.prop('disabled', false);
                    curso.selectpicker('refresh');
                });
            }

            function getQueryAjax(url) {
                if (tableFinalista.children('tbody').length > 0)
                    tableFinalista.DataTable().clear().destroy();
                tableFinalista.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    buttons: ['colvis', 'excel', {
                        text: '<i class="fas fa-plus-square"></i> Criar confirmação de matrícula finalista',
                        className: 'btn-primary main ml-1 rounded btn-main',
                        action: function(e, dt, node, config) {
                            if(redirect != "") window.open(redirect, "_blank");
                        }
                    }, {
                        text: '<i class="fas fa-trash"></i> Anulação finalista',
                        className: 'btn-primary main ml-1 rounded btn-main',
                        action: function(e, dt, node, config) {
                            window.open($('#anulate-matriculation').attr('href'), "_blank");
                        }
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'num_confirmaMatricula',
                        name: 'num_confirmaMatricula'
                    }, {
                        data: 'matricula',
                        name: 'matricula'
                    }, {
                        data: 'name_full',
                        name: 'name_full'
                    }, {
                        data: 'email',
                        name: 'email'
                    }, {
                        data: 'display_name',
                        name: 'display_name'
                    }, {
                        data: 'year_curso',
                        name: 'year_curso',
                        visible: false
                    }, {
                        data: 'num_bi',
                        name: 'num_bi'
                    }, {
                        data: 'name_full_creat',
                        name: 'name_full_creat',
                        visible: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at'
                    }, {
                        data: 'status',
                        name: 'status',
                        searchable: false
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }],
                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    },
                });
            }
        })();
    </script>
@endsection
