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
        <label for="lective_year_select">Selecione o ano lectivo</label>
        <select name="lective_year_select" id="lective_year_select" class="selectpicker form-control form-control-sm" style="width: 100% !important">
            <option value="">Selecione o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" 
                    {{ $lectiveYearSelected == $lectiveYear->id ? 'selected' : '' }}>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3 d-none">
        <button type="button" class="btn btn-success btn-sm" id="openModalBtn">
            <i class="fas fa-plus-circle me-1"></i>Criar tipo de convite
        </button>
    </div>
@endsection

@section('body')
    <form action="{{ route('requerimento.solicitacao_convite_store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col">
                <div class="card p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Estudante</label>
                            <select name="student_id" id="student_id" class="selectpicker form-control form-control-sm" data-live-search="true">
                                <option value="" selected>Selecione o Estudante</option>
                                @foreach ($estudantes as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} #{{ $student->number }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-none" id="invitation_type_container">
                            <label>Tipo de Convite</label>
                            <select name="invitation_type_id" id="invitation_type_id" class="selectpicker form-control form-control-sm" data-live-search="true">
                                <option value="" selected>Selecione o Tipo de Convite</option>
                                <!-- Preenchido dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Quantidade</label>
                            <input type="number" name="quantidade" class="form-control form-control-sm" min="1" value="1" required>
                        </div>
                    </div>
                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i>Requerer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="lective_year" id="lective_year" value="{{ $lectiveYearSelected }}">
    </form>

    <!-- Modal Criar Tipo de Convite -->
    <div id="invitationModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Criar Tipo de Convite</h5>
                    <button type="button" class="btn-close" id="closeModalBtn"></button>
                </div>
                <div class="modal-body">
                    <form id="invitationForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo:</label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">Selecionar</option>
                                @foreach ($articleTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->code }}#{{ $type->base_value }}Kz</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Criar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modal = new bootstrap.Modal(document.getElementById('invitationModal'));
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        const form = document.getElementById('invitationForm');

        openBtn.addEventListener('click', () => modal.show());
        closeBtn.addEventListener('click', () => modal.hide());

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const type = document.getElementById('type').value;
            const lective_year = document.getElementById('lective_year').value;

            fetch('/pt/avaliations/requerimento/create_convite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name, type, lective_year })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.success);
                window.location.reload();
            })
            .catch(err => console.error(err));

            form.reset();
            modal.hide();
        });

        // Função para carregar tipos de convite conforme o ano lectivo
        function carregarTiposConvite(lective_year_id) {
            if (!lective_year_id) return;

            const invitationSelect = document.getElementById('invitation_type_id');
            const container = document.getElementById('invitation_type_container');

            // Limpar opções anteriores
            invitationSelect.innerHTML = '<option value="" selected>Selecione o Tipo de Convite</option>';

            fetch(`/pt/avaliations/requerimento/get_convite/${lective_year_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.name;
                            invitationSelect.appendChild(option);
                        });

                        // Seleciona automaticamente o primeiro tipo disponível
                        invitationSelect.selectedIndex = 1;

                        // Mostra o campo e atualiza o selectpicker
                        container.classList.remove('d-none');
                        $('.selectpicker').selectpicker('refresh');
                    } else {
                        container.classList.add('d-none');
                    }
                })
                .catch(error => console.error('Erro ao carregar tipos de convite:', error));
        }

        // Quando mudar o ano lectivo
        document.getElementById('lective_year_select').addEventListener('change', function() {
            const lective_year_id = this.value;
            document.getElementById('lective_year').value = lective_year_id;
            carregarTiposConvite(lective_year_id);
        });

        // Ao carregar a página, chamar automaticamente com o ano actual selecionado
        document.addEventListener('DOMContentLoaded', () => {
            const selectedYear = document.getElementById('lective_year_select').value;
            if (selectedYear) carregarTiposConvite(selectedYear);
        });
    </script>
@endsection
