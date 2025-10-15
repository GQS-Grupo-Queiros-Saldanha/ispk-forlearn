@extends('layouts.backoffice')

@section('content')
   @foreach ($tb_calendario as $items)    
   @endforeach
   
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            EDITAR CALENDÁRIO </h1>
                    </div>
                    <div class="pb-0 col-6 ">
                        <div class="float-right mr-4">
                            <ol class="breadcrumb float-rigth">
                                @if ($menu_activo==true)
                                    <li class="breadcrumb-item"><a
                                        href="//forlearn.ao/pt/avaliations/avaliacao">Avaliações</a>
                                    </li>
                                @else   
                                @endif
                                <li class="breadcrumb-item"><a
                                        href="//forlearn.ao/pt/avaliations/school-exam-calendar">Calendário de prova</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{$items->display_name}}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <form method="POST" action="{{route('school-exam-calendar_update') }}">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="form-group col">
                                        <label for="codigo">C&oacute;digo</label>
                                        <input value="{{$items->code}}" class="form-control" placeholder="" 
                                            autocomplete="code" name="codigo" type="text" id="codigo">
                                    </div>
                                    <div class="form-group col">
                                        <label for="nome">Nome da prova</label>
                                        <input value="{{$items->display_name}}" class="form-control" placeholder="" 
                                            autocomplete="display_name" name="nome" type="text" id="nome">
                                    </div>
                                </div>
                                <div class="card-body row pt-0">
                                    <div class="form-group col" style="position: relative;">
                                        <label for="data_start">Data do in&iacute;cio </label>
                                        <input class="form-control" value="{{$items->date_start}}" 
                                            autocomplete="date_start" name="data_start" type="date" id="data_start">
                                    </div>
                                    <div class="form-group col" style="position: relative;">
                                        <label for="data_end">Data do fim </label>
                                        <input class="form-control" value="{{$items->data_end}}" 
                                            autocomplete="data_end" name="data_end" type="date" id="data_end">
                                    </div>
                                    <input type="hidden" name="id_avaliacao" value="{{$items->id_avaliacao}}">
                                </div>
                            </div>
                            <div class="card-footer  ">
                                <button type="submit" class="btn btn-success p-2 mb-1 mr-3 float-right" id="editUser">
                                    @icon('fas fa-edit')
                                    @lang('common.edit')
                                </button>
                            </div>
                        </div>
                    </div>
                   
                </form>
            </div>
        </div>
    @endsection