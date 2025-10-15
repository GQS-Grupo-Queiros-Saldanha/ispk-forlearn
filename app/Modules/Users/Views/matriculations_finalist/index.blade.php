@extends('layouts.generic_index_new')
@section('navbar')

@endsection
@section('page-title')
    @lang('Users::matriculations.create_matriculation') finalista - {{ $lectiveYears->display_name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('index.matriculation-finalista') }}">Finalista</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('body')
    <form method="POST" action="{{ route('create.New-matriculation-finalist') }}">
        @csrf
        <input type="hidden" name="ano_lectivo" value="{{ $lectiveYears->id }}">
        <div class="row m-1">
            <div class="form-group col-4">
                <label for="exampleInputEmail1">Estudante</label>
                <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                    id="user" data-actions-box="false" data-selected-text-format="values" name="user"
                    tabindex="-98">
                    <option value="0" selected></option>
                    @foreach ($getStudent as $item)
                        <option value="{{ $item->id_matricula }}">{{ $item->full_nameEmail }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-3">
                <button hidden type="submit" class="btn btn-primary main rounded div-btn-confirme mt-3">
                    <i class="fas fa-plus-square"></i>
                    Criar confirmação da matrícula
                </button>
            </div>
        </div>
    </form>
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>
        (() => {
            $("#user").change((e) => {
                let value = $("#user").val()
                value != 0 ? $(".div-btn-confirme").attr('hidden', false) : $(".div-btn-confirme").attr(
                    'hidden', true)
            });
        })();
    </script>
@endsection
