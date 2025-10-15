@section('title', __('Estatísticas docente'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('RH::index_menu')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>ANÁLISE ESTATÍSTICO DOS DOCENTES </h1>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                    </div>
                </div>


                <div class="row">

                    <div class="col">
                        {!! Form::open([
                            'route' => ['recurso-humanos.estatistica.generate'],
                            'method' => 'post',
                            'required' => 'required',
                            'target' => '_blank',
                        ]) !!}
                        @csrf
                        @method('post')
                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('GA::courses.course')</label>
                                        {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required' => 'required']) }}
                                    </div>
                                </div>

                            </div>

                        </div>

                        <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">




                    </div>
                    <div class="col-12 justify-content-md-end">

                        <div class="form-group col-4  justify-content-md-end" style="float:right;">
                            <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank"
                                style="width:180px;">
                                <i class="fas fa-file-pdf"></i>
                                Gerar PDF
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}
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
