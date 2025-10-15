<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PERCURSO ACADÉMICO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Percurso académico</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <style>
        body {
            font-family: "sans-serif";
        }

        .table td,
        .table th {
            padding: 2px;
        }

        .h1-title {
            padding: 0;
            margin-bottom: 0;
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
        }

        .div-top {
            text-transform: uppercase;
            position: relative;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }
    </style>
@endsection
@section('body')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                ×
            </button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div id="showSelect" class="row">
        <div class="col-6 p-2">
            <label>Curso:</label>
            <select class="selectpicker form-control" name="course_id" id="course_id">
                @foreach ($student as $item)
                    <option value="{{ $item->course_id }}" selected>
                        {{ $item->course }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Estudantes:</label>
            <select class="selectpicker form-control" name="students" id="students" required>
                @foreach ($student as $item)
                    <option value="{{ $item->id }}" selected>
                        {{ $item->name . ' #' . $item->number . ' (' . $item->email . ')' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col" id="btn-print">
            <div class="float-right mr-3">
                <a type="submit" class="btn btn-success mb-3 ml-3" href="academic-path/{{ $student[0]->id }}"
                    id="btn-link" target="_blank" route rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Imprimir documento
                </a>
            </div>
        </div>
    </div>
@endsection
