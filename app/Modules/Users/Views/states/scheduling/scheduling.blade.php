@section('title',"Agendar estados")
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
                    <div class="col-sm-9">
                        <h1 class="m-0 text-dark">Criar estados</h1>
                    </div>
                    <div class="col-sm-3">
                       <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('indexScheduling.state') }}">Agendar estados</a></li>
                                <li class="breadcrumb-item active" aria-current="page">criar</li>
                            </ol>
                        </nav>
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
                               {!! Form::open(['route' => ['updateScheduling.state', $task->id], 'method' => 'put']) !!}
                                    <div class="form-group">
                                        <label for="name" class="col-form-label">Tarefa:</label>
                                        <input type="text" class="form-control col-4" name="task" value="{{ $task->task}}" readonly>
                                    </div>
                                    <div class="form-group">
                                    <label for="state" class="col-form-label">1ª Data: {{ $task->id == 3 ? '(Gerar o emolumento de cobrança de propina)' : '(Mudar estado para aguardar matrícula)'}}</label>
                                        <select name="first_date" id="" class="form-control col-1">
                                            @for ($i = 1; $i <= 31; $i++)
                                                <option value="{{$i}}" {{ $task->first_date == $i ? 'selected' : $i}}> {{ $i }}</option>
                                            @endfor
                                        </select>
                                        
                                        <select name="first_month" id="" class="form-control col-1" {{ $task->id == 3 ? 'hidden' : ''}} >
                                            <option value="01" {{ $task->first_month == '01' ? 'selected' : ''}}> Janeiro</option>
                                            <option value="02" {{ $task->first_month == '02' ? 'selected' : ''}}> Fevereiro</option>
                                            <option value="03" {{ $task->first_month == '03' ? 'selected' : ''}}> Março</option>
                                            <option value="04" {{ $task->first_month == '04' ? 'selected' : ''}}> Abril</option>
                                            <option value="05" {{ $task->first_month == '05' ? 'selected' : ''}}> Maio</option>
                                            <option value="06" {{ $task->first_month == '06' ? 'selected' : ''}}> Junho</option>
                                            <option value="07" {{ $task->first_month == '07' ? 'selected' : ''}}> Julho</option>
                                            <option value="08" {{ $task->first_month == '08' ? 'selected' : ''}}> Agosto</option>
                                            <option value="09" {{ $task->first_month == '09' ? 'selected' : ''}}> Setembro</option>
                                            <option value="10" {{ $task->first_month == '10' ? 'selected' : ''}}> Outubro</option>
                                            <option value="11" {{ $task->first_month == '11' ? 'selected' : ''}}> Novembro</option>
                                            <option value="12" {{ $task->first_month == '12' ? 'selected' : ''}}> Dezembro</option>
                                        </select>
                                        
                                    </div>

                                     <div class="form-group">
                                        <label for="state" class="col-form-label">2ª Data: {{ $task->id == 3 ? '(Mudar o estado para aguardar pagamento)' : '(Mudar estado para não inscrito)'}}</label>
                                        <select name="second_date" id="" class="form-control col-1">
                                            @for ($i = 1; $i <= 31; $i++)
                                                <option value="{{$i}}" {{ $task->second_date == $i ? 'selected' : $i}}> {{ $i }}</option>
                                            @endfor
                                        </select>
                                        
                                        <select name="second_month" id="" class="form-control col-1" {{ $task->id == 3 ? 'hidden' : ''}}>
                                            <option value="01" {{ $task->second_month == '01' ? 'selected' : ''}}> Janeiro</option>
                                            <option value="02" {{ $task->second_month == '02' ? 'selected' : ''}}> Fevereiro</option>
                                            <option value="03" {{ $task->second_month == '03' ? 'selected' : ''}}> Março</option>
                                            <option value="04" {{ $task->second_month == '04' ? 'selected' : ''}}> Abril</option>
                                            <option value="05" {{ $task->second_month == '05' ? 'selected' : ''}}> Maio</option>
                                            <option value="06" {{ $task->second_month == '06' ? 'selected' : ''}}> Junho</option>
                                            <option value="07" {{ $task->second_month == '07' ? 'selected' : ''}}> Julho</option>
                                            <option value="08" {{ $task->second_month == '08' ? 'selected' : ''}}> Agosto</option>
                                            <option value="09" {{ $task->second_month == '09' ? 'selected' : ''}}> Setembro</option>
                                            <option value="10" {{ $task->second_month == '10' ? 'selected' : ''}}> Outubro</option>
                                            <option value="11" {{ $task->second_month == '11' ? 'selected' : ''}}> Novembro</option>
                                            <option value="12" {{ $task->second_month == '12' ? 'selected' : ''}}> Dezembro</option>
                                        </select>
                                        
                                    </div>
                                    @if ($task->id == 3)
                                        <div class="form-group">
                                        <label for="past_day" class="col-form-label"> 3ª Data: (Mudar o estado para inativo após <strong>{{ $task->past_day}}</strong> dias da 2ª data)</label>
                                        <select name="past_day" id="past_day" class="form-control col-1">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                        </select>
                                        </div>
                                    @endif

                                    <div class="form-group float-right">
                                        <button type="submit" id="submit" class="btn btn-success">Salvar</button>
                                    </div>
                                {!! Form::close() !!}
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

