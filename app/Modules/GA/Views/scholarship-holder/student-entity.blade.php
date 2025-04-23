@section('title',__('Gerir bolsas de estudo'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<div class="content-panel" style="padding: 0px">
    @include('Payments::requests.navbar.navbar')
        <div class="content-header"> 
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">{{--@lang('GA::schedules.schedules')--}}
                            Associar estudante a uma entidade bolseira
                        </h1>
                        <span class="text-muted"></span>
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

                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('store.associate.student') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="">Estudante</label>
                                            <input type="text" class="form-control" name="company" value="{{ $user->name }}" readonly>
                                            <div hidden>
                                                <input type="text" value="{{ $user->id }}" name="user_id">
                                            </div>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="">Entidate</label>
                                            <select name="entity" id="" class="form-control">
                                                <option value=""> --- </option>
                                                @foreach ($entitys as $entity)
                                                    <option value="{{ $entity->id }}">
                                                        {{ $entity->company}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success float-right">Salvar</button>
                                </form>
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
       /* $(document).ready( function () {
            $('#holder-table').DataTable();
        });*/
    </script>
@endsection
