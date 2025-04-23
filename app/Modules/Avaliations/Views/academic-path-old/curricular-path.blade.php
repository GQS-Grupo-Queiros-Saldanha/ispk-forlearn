@section('title',__('Historico academico'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Histórico Acadêmico</h1></h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right mt-4">
                    <a href="{{ route('old_student.index') }}" class="btn btn-secondary btn-sm mb-3">
                                Voltar
                            </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead >
                            <tr>
                                <th class="text-center">Nº de matrícula</th>
                                <th class="text-center">Nome completo</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Número</th>
                                <th class="text-center">Curso</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td class="text-center">{{ $studentInfo->matriculation->code}}</td>
                            <td class="pl-3">{{ $personalName }}</td>
                            <td class="text-center">{{ $studentInfo->email }}</td>
                            <td class="text-center">{{ $matriculationCode }}</td>
                            <td class="text-center">
                                @foreach ($studentInfo->courses as $course)
                                    {{$course->currentTranslation->display_name}}
                                 @endforeach
                            </td>
                            <td class="text-center"> --- </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <th class="text-center">Ano</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Disciplina</th>
                                    <th class="text-center ">Ano lectivo</th>
                                    <th class="text-center ">Nota</th>
                                </thead>
                                <tbody>
                                    @foreach ($disciplines as $discipline)
                                        <tr>
                                            <td class="text-center">{{ $discipline->year}} º</td>
                                            <td class="text-center">{{ $discipline->code }}</td>
                                            <td class="pl-3">{{ $discipline->name }}</td>
                                            <td class="text-center ">{{ $discipline->lective_year}}</td>
                                            <td class="text-center ">{{ $discipline->grade}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <div class="float-right">
                                <a href="{{ route('old_student.print', $studentInfo->id) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-print"></i>
                                    Imprimir
                                </a>
                            </div>
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





   $(document).ready(function()
   {
    /*var body = '';

    for (let a = 0; a < students.length; a++) {
        body += "<tr>"
        body += "<td>"+students[a].year+"</td>"
        var discipline_id = students[a].discipline_id
        var lective_year = students[a].lective_year
        lectiveYears.push(students[a].lective_year)
        for (let b = 0; b < disciplineCode.length; b++) {
            if (disciplineCode[b].discipline_id == discipline_id) {
                body += "<td>"+disciplineCode[b].discipline_code+"</td>"
            }
        }
        for (let c = 0; c < disciplines.length; c++) {
            if (disciplines[c].discipline_id == discipline_id) {
                body += "<td>"+disciplines[c].discipline_name+"</td>"
            }
        }


          for (let d = 0; d < lect.length; d++) {

              body += "<td>##</td>"

        }


        body += "</tr>"

    }
    $("#body").append(body);*/
   });
    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection
