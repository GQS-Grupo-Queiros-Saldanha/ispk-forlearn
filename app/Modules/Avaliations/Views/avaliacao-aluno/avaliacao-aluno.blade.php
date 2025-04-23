@section('title',__('Avaliação Alunos'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Avaliação Alunos</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">

                    <a href="{{ route('avaliacao_aluno.create') }}" class="btn btn-primary btn-sm mb-3">
                        <i class="fas fa-plus-square"></i>
                        @lang('common.new')
                    </a>

                    <div class="card">
                        <div class="card-body">
                            @php $i = 1; @endphp
                            <table id="avaliacao-tables" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº de Ordem</th>
                                        <th>Avaliação</th>
                                        <th>Edição de Plano de Estudos</th>
                                        <th>Disciplina</th>
                                        <th>Criado Por</th>
                                        <th>Editado Por</th>
                                        <th>Criado a</th>
                                        <th>Editado a</th>
                                        <th colspan="3">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal confirm --}}
@include('layouts.backoffice.modal_confirm')


@endsection



@section('scripts')
@parent
<script>
    
    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection
