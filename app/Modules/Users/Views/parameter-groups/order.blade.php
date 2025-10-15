@section('title',__('Users::parameter-groups.parameter_groups'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<div class="content-panel" style="padding: 0;">
    @include('Users::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Users::parameter-groups.parameter_groups')</h1>
                        <span class="text-muted">Pode reordenar os grups de parâmetros abaixo, arrastando-os.</span>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('parameter-groups') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {!! Form::open(['route' => ['parameter-groups.save_order'], 'id' => 'form-parameter-groups-order', 'method' => 'put']) !!}
                        <button type="submit" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-save')
                            @lang('common.save')
                        </button>
                        {!! Form::close() !!}


                        <div class="card">
                            <div class="card-body">
                                @if(count($parameter_groups) > 0)
                                    <ol class="list-group list-group-sortable" id="items">
                                        @foreach($parameter_groups as $parameter_group)
                                            <li class="list-group-item" style="cursor: move" data-id="{{ $parameter_group->id }}">{{ $parameter_group->translation->display_name }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    ...
                                @endif
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
        let el = document.getElementById('items');
        let sortable = new Sortable(el, {
            ghostClass: 'bg-warning',
        });

        $(function(){

            $('#form-parameter-groups-order').on('submit', function(e) {
                e.preventDefault();

                let $self = $(this);

                let $button = $self.find('button');

                let _method = $self.find('input[name=_method]').val();
                let _token = $self.find('input[name=_token]').val();

                $.ajax({
                    url: $self.attr('action'),
                    method : 'POST',
                    responseType: 'json',
                    data: {
                        _method: _method,
                        _token: _token,
                        parameter_groups: sortable.toArray()
                    },
                    beforeSend: function () {
                        $button.removeClass('btn-danger').addClass('btn-success');
                        $button.find('i').removeClass('fa-save fa-check fa-times').addClass('fa-sync fa-spin');
                    }
                }).done(function () {
                    $button.find('i').addClass('fa-check').removeClass('fa-sync fa-spin');
                }).fail(function () {
                    $button.removeClass('btn-success').addClass('btn-danger');
                    $button.find('i').addClass('fa-times').removeClass('fa-sync fa-spin');
                });
            });
        });

    </script>
@endsection
