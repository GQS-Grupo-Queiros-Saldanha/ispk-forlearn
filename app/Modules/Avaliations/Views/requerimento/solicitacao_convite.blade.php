@extends('layouts.generic_index_new', ['breadcrumb_super' => true])
@section('title', __('Solicitação de Convite'))

@section('page-title')
    @lang('Solicitação de Convite')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('requerimento.index') }}">Requerimentos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Convite</li>
@endsection

@section('selects')
    <div class="mb-2 mt-3">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->id }}" selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->id }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <button type="button" class="btn btn-success btn-sm" id="openModalBtn">
            <i class="fas fa-plus-circle me-1"></i>Criar tipo de convite
        </button>
    </div>
@endsection

@section('body')
    <form action="{{ route('requerimento.solicitacao_revisao_prova_store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>Estudante</label>
                                <select name="student_id" id="students" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="" selected>Selecione o Estudante</option>
                                    <!--Colocado pelo JS-->
                                </select>
                            </div>
                            <div class="form-group col">
                                <label>Tipo de Convite</label>
                                <select name="student_id" id="students" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="" selected>Selecione o Tipo de Convite</option>
                                    @foreach ($invitation as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="form-group col">
                                <label>Quantidade</label>
                                <input type="number" name="quantidade" class="form-control form-control-sm" min="1" value="1" required>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="float-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i>Requerer
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
<!-- O Modal -->
<div id="invitationModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background-color:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    
    <div style="background:white; padding:20px; border-radius:8px; width:300px; position:relative;">
        <h2>Criar Tipo de Convite</h2>
        <form id="invitationForm">
            <!-- Input de nome -->
            <div style="margin-bottom:10px;">
                <label for="name">Nome:</label><br>
                <input type="text" id="name" name="name" required>
            </div>

            <!-- Selector -->
            <div style="margin-bottom:10px;">
                <label for="type">Tipo:</label><br>
                <select id="type" name="type" required>
                    <option value="">Selecionar</option>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                </select>
            </div>

            <!-- Botão de criar -->
            <button type="submit">Criar</button>
        </form>

        <!-- Botão para fechar modal -->
        <button id="closeModalBtn" style="position:absolute; top:10px; right:10px;">X</button>
    </div>
</div>

@section('scripts')
    @parent
<script>
    const modal = document.getElementById('invitationModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const form = document.getElementById('invitationForm');

    // Abrir modal
    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    // Fechar modal
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Submeter form
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('name').value;
        const type = document.getElementById('type').value;
        console.log('Criar convite:', { name, type });
        // Aqui podes adicionar fetch/AJAX para enviar os dados ao servidor
        modal.style.display = 'none';
        form.reset();
    });

</script>

@endsection