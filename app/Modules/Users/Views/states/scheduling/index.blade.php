@section('title', 'Agendar estados')
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel" style="padding: 0;">
        @include('Users::states.navbar.navbar')  
        <div class="content-header">
            <div class="container-fluid"> 
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Agendar estados</h1>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body">
                                @php $i = 1; @endphp
                                <table id="students-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tarefa</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $task->task }}</td>
                                                <td>
                                                    <a href="{{ route('editScheduling.state', $task->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <span class="fa fa-clock"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
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

@endsection
