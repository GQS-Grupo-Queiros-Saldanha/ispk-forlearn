<title>Candidaturas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Fases de candidatura')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidate.list_candidatura') }}">Calendários</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Fases</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year_analisar" id="lective_year_analisar" class="selectpicker form-control form-control-sm">
            @if(!isset($only)) <option selected value="">Nenhum registro</option> @endif
            @foreach ($lectiveYears as $lectiveYear)
                @isset($only)
                    @if ($lectiveYearSelected == $lectiveYear->id)
                        <option value="{{ $lectiveYear->id }}" selected>
                            {{ $lectiveYear->currentTranslation->display_name }}
                        </option>
                        @break
                    @endif
                @else
                    <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endisset
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @include('Users::candidate.fase_candidatura.message')
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>ano lectivo</th>
                <th>data_inicio</th>
                <th>data_fim</th>
                <th>fase</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>

    <div class="modal" id="modalFase" tabindex="-1" role="dialog">
        <form class="modal-dialog" role="document" id="form" action="{{ route('fase.candidatura.store') }}"
            method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="chave" id="chave" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Criação de Fase</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning alert-dismissible fade show d-none" role="alert" id="alert">
                        <span id="alert-message"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="">Número da fase: (campo automático)</label>
                        <input type="number" class="form-control" name="fase_num" id="fase_num" readonly
                            @isset($lectiveYearCandidatura->fase) 
                                min="{{ $lectiveYearCandidatura->fase + 1 }}" value="{{ $lectiveYearCandidatura->fase + 1 }}" fase="{{ $lectiveYearCandidatura->fase + 1 }}"
                            @endisset 
                        />
                    </div>
                    <div class="form-group" hidden>
                        <input type="hidden" name="lective_year" id="lective_year"
                            @isset($lectiveYearSelected) value="{{ $lectiveYearSelected }}" @endisset />
                        @isset($lectiveYearCandidatura->first)
                            <input type="hidden" name="first" id="first" value="1" />
                        @endisset
                    </div>
                    <div class="form-group">
                        <label for="">Data inicio</label>
                        <input type="date" name="data_start" id="data_start" class="form-control"
                            @isset($lectiveYearCandidatura->data_fim) value="{{ $lectiveYearCandidatura->data_fim }}" @endisset />
                    </div>
                    <div class="form-group">
                        <label for="">Data fim</label>
                        <input type="date" name="data_end" id="data_end" class="form-control"
                            @isset($lectiveYearCandidatura->data_fim) value="{{ $lectiveYearCandidatura->data_fim }}" @endisset />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-fase-action">guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">cancelar</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('hiddens')
    <input type="hidden" name="" value="{{ route('fase.candidatura.ajax.list') }}" id="ajax_fase_list">
@endsection
@section('scripts-new')
    @parent
    <script>
        const form = $('#form');
        const formMethod = $("[name='_method']");
        const btnFase = $('#btn-fase');
        const modalFase = $('#modalFase');
        const numFase = $('#fase_num');
        const dataEnd = $('#data_end');
        const dataStart = $('#data_start');
        const lectiveYear = $('#lective_year_analisar');
        const btnFaseAction = $('#btn-fase-action');
        const lective_year = $('#lective_year');

        const alert = $('#alert');
        const alertMessage = $('#alert-message');

        let ano = lectiveYear.val();
        
        if (ano != "" && ano != null) {
            reloadDatas(ano);
        }

        btnFase.click((e) => {
            modalFase.modal('show');
            form.attr('action', '{{ route('fase.candidatura.store') }}');
            $("#form [name='_method']").val('POST');
            let num = numFase.att("fase");
            numFase.val(num).attr('min',num);
        });


        lectiveYear.change((e) => {
            let ano = lectiveYear.val();
            if (ano != "") {
                validarAnoLective(ano);
                lective_year.val(ano);
                reloadDatas(ano);
            } else {
                if (!alert.hasClass('d-none')) alert.addClass('d-none');
                reloadDatas();
            }
        })

        function validarAnoLective(ano) {
            $.ajax({
                url: '{{ route('fase.candidatura.ajax.get.year') }}',
                type: "GET",
                data: { year: ano },
                cache: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 1) {
                        let body = response.body;
                        let dataActual = Date.now();
                        let dataFim = new Date(body.end_date);

                        numFase.val(body.fase + 1).attr('min', body.fase + 1);
                        dataStart.val(body.data_fim).attr('min', body.data_fim);
                        dataEnd.val(body.data_fim).attr('min', body.data_fim).attr('max', body.end_date);

                        if (dataActual < dataFim) {
                            if (!body.is_termina) {
                                alertMessage.html( `A fase anterior se encontra activo, apenas será perimitido que faças candidatura apartir da date de termino da fase anterior (${body.data_fim})` );
                                alert.removeClass('d-none');
                                btnFaseAction.removeClass('d-none');
                            } else {
                                alert.addClass('d-none');
                                btnFaseAction.removeClass('d-none');
                            }
                        } else {
                            alertMessage.html('O Ano lectivo selecionado já se encontra encerrado.');
                            alert.removeClass('d-none');
                            if (!btnFaseAction.hasClass('d-none')) btnFaseAction.addClass('d-none');
                        }
                    } else if (response.status == 2) {
                        let body = response.body;
                        numFase.val(1).attr('min', 1).attr('max', 1);
                        dataEnd.val(body.end_date).attr('max', body.end_date);
                        dataStart.val(body.start_date).attr('min', body.start_date);
                    }
                }
            });
        }

        function reloadDatas(lective_year = "") {
            let perm = lective_year != "" ? "?lective_year=" + lective_year : "";
            let table = $('#users-table');
            let tam = table.children('tbody').length;
            if (tam > 0) table.DataTable().clear().destroy();
            table.DataTable({
                ajax: `${$('#ajax_fase_list').val()+perm}`,
                buttons: ['colvis', 'excel', , {
                    text: '<i class="fas fa-plus-square"></i> Criar fase candidatura',
                    className: 'btn-primary main ml-1 rounded',
                    action: function(e, dt, node, config) {
                        modalFase.modal('show');
                        form.attr('action', '{{ route('fase.candidatura.store') }}');
                        formMethod.val('POST');
                        let num = numFase.attr("fase");
                        numFase.val(num).attr('min',num);                        
                    }
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        searchable: true
                    },
                    {
                        data: 'data_inicio',
                        name: 'data_inicio',
                        searchable: true
                    }, {
                        data: 'data_fim',
                        name: 'data_fim',
                        visible: true,
                        searchable: true
                    },
                    {
                        data: 'fase',
                        name: 'fase',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        }
        
    </script>
@endsection
