@section('title',__('Matrícula finalista'))

@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<div class="content-panel" style="padding: 0px;">
    @include('Users::matriculations.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @lang('Users::matriculations.create_matriculation') finalista - {{$lectiveYears->display_name}}
                        </h1>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end pr-5">
                        <a href="#" class="text-muted"> Home </a>
                        <a href="pt/users/matriculations" class="pl-2"> / Matrícula </a>
                        <a href="#" class="pl-2">  / Lista dos finalista</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col pr-3 pl-3">
                        <div class=" pl-0 col">
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
@endsection

@section('scripts')
    @parent
    <script>

    </script>
   
@endsection

