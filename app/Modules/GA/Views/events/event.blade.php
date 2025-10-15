@section('title', __('Eventos'))
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        a: {}

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }
    </style>





    <div class="content-panel" style="padding:0">
        @include('GA::events.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
                                
                                <li class="breadcrumb-item active" aria-current="page">
                                    @switch($action)
                                        @case('create')
                                           Criar
                                        @break

                                        @case('show')
                                            Ver
                                        @break

                                        @case('edit')
                                            Editar
                                        @break
                                    @endswitch
                                </li>

                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1> @switch($action)
                                @case('create')
                                    @lang('GA::events.create_event')
                                @break

                                @case('show')
                                    @lang('GA::events.event')
                                @break

                                @case('edit')
                                    @lang('GA::events.edit_event')
                                @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-12">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['events.store']]) !!}
                    @break

                    @case('show')
                        {!! Form::model($event) !!}
                    @break

                    @case('edit')
                        {!! Form::model($event, ['route' => ['events.update', $event->id], 'method' => 'put']) !!}
                    @break
                @endswitch







                <div class="col-12">
                    <div class="row">
                        <div class="form-group col-12">


                            {{-- Criar um determinado evento --}}

                            @switch($action)
                                @case('create')
                                    @foreach ($languages as $language)
                                        <div class="tab-pane col-12 @if ($language->default) active show @endif"
                                            id="language{{ $language->id }}">

                                            <div class="row">
                                                <div class="col-6">

                                                    <div class="col-12">
                                                        {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                                    </div>

                                                    {{-- Tipo de evento --}}

                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="event_type">Tipo de evento</label>
                                                            <select class="selectpicker form-control form-control-sm"
                                                                name="event_type" id="event_type" data-actions-box="true"
                                                                data-live-search="true">
                                                                <option></option>
                                                                @foreach ($event_types as $item)
                                                                    <option value="{{ $item->event_type_id }}">
                                                                        {{ $item->display_name }}</option>
                                                                @endforeach

                                                            </select>

                                                        </div>
                                                    </div>



                                                    {{-- Lista de Cargos --}}

                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="roles">Cargos:</label>
                                                            <select class="selectpicker form-control form-control-sm" name="roles"
                                                                id="roles" data-actions-box="true" data-live-search="true"
                                                                multiple>

                                                                @foreach ($roles as $item)
                                                                    <option value="{{ $item->role_id }}">
                                                                        {{ $item->display_name }}</option>
                                                                @endforeach

                                                            </select>

                                                        </div>
                                                    </div>

                                                    {{-- Destinatários --}}

                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="users_group">Destinatário(s):</label>
                                                            <select class=" selectpicker form-control form-control-sm"
                                                                name="users_group[]" id="users_group" data-actions-box="true"
                                                                data-live-search="true" multiple disabled>

                                                            </select>
                                                         
                                                        </div>
                                                    </div>


                                                    <div class="col-12">

                                                        <div class="row">
                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start', $event->start ?? '', ['type' => 'date', 'placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.start_date')], ['class' => 'form-control col-6']) }}
                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end', $event->end ?? '', ['type' => 'date', 'placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.end_date')]) }}
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start_time', '', ['type' => 'time', '', '', 'required'], ['label' => __('Hora de início')]) }}
                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end_time', '', ['type' => 'time', '', '', 'required'], ['label' => __('Hora de fim')]) }}
                                                            </div>
                                                            <div class="col-6">
                                                                <button type="submit"
                                                                    class="create-event btn ml-3 btn-success mb-3 submit">
                                                                    @icon('fas fa-plus-circle')
                                                                    @lang('common.create')
                                                                </button>


                                                            </div>

                                                        </div>
                                                    </div>


                                                </div>

                                                <div class="col-6">
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="description[' . $language->id . ']'">Descrição do
                                                                evento</label>
                                                            {{ Form::textarea('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                @break

                                @case('edit')
                                    @foreach ($languages as $language)
                                        <div class="tab-pane col-12 @if ($language->default) active show @endif"
                                            id="language{{ $language->id }}">

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="col-12">
                                                        {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="event_type">Tipo de evento</label>
                                                            <select class="selectpicker form-control form-control-sm"
                                                                name="event_type" id="event_type" data-actions-box="true"
                                                                data-live-search="true">

                                                                @foreach ($event_types as $item)
                                                                    @if ($event->event_type_id == $item->event_type_id)
                                                                        <option value="{{ $item->event_type_id }}" selected>
                                                                            {{ $item->display_name }}</option>
                                                                    @else
                                                                        <option value="{{ $item->event_type_id }}">
                                                                            {{ $item->display_name }}</option>
                                                                    @endif
                                                                @endforeach


                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="roles">Cargos:</label>
                                                            <select class="selectpicker form-control form-control-sm" name="roles[]"
                                                                id="roles" data-actions-box="true" data-live-search="true"
                                                                multiple>

                                                                
                                                                @foreach ($roles as $item)
                                                                    @if (in_array($item->role_id, $cargos))
                                                                        <option value="{{ $item->role_id }}" selected>
                                                                            {{ $item->display_name }}</option>
                                                                    @else
                                                                        <option value="{{ $item->role_id }}">
                                                                            {{ $item->display_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                                

                                                            </select>

                                                        </div>
                                                    </div>

                                                    {{-- Destinatários --}}

                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="users_group">Destinatário(s):</label>
                                                            <select class=" selectpicker form-control form-control-sm"
                                                                name="users_group[]" id="users_group" data-actions-box="true"
                                                                data-live-search="true" multiple>
                                                                @foreach ($usuarios_cargos as $item)
                                                                    @if (in_array($item->id_usuario, $usuarios_id))
                                                                        <option value="{{ $item->id_usuario }}-{{ $item->cargo_usuario }}" selected>
                                                                            {{ $item->nome_usuario }} (
                                                                            {{ $item->email_usuario }}
                                                                            )</option>
                                                                    @else
                                                                        <option value="{{ $item->id_usuario }}-{{ $item->cargo_usuario }}">
                                                                            {{ $item->nome_usuario }} (
                                                                            {{ $item->email_usuario }}
                                                                            )</option>
                                                                    @endif
                                                                @endforeach

                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-12">


                                                        <div class="row">

                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start', $event->start ?? '', ['type' => 'date', 'placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.start_date')], ['class' => 'form-control col-6']) }}

                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end', $event->end ?? '', ['type' => 'date', 'placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.end_date')]) }}
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-12">


                                                        <div class="row">

                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start_time', $event->start_time ?? '', ['type' => 'time', 'required'], ['label' => __('Hora de início')]) }}

                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end_time', $event->end_time ?? '', ['type' => 'time', 'required'], ['label' => __('Hora de fim')]) }}
                                                            </div>
                                                            <div class="col-6">
                                                                <button type="submit" class="btn ml-3 btn-success mb-3 submit">
                                                                    @icon('fas fa-save')
                                                                    @lang('common.save')
                                                                </button>

                                                            </div>

                                                        </div>
                                                    </div>


                                                </div>
                                                <div class="col-6">
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="description[' . $language->id . ']'">Descrição do
                                                                evento</label>
                                                            {{ Form::textarea('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                @break

                                @case('show')
                                    @foreach ($languages as $language)
                                        <div class="tab-pane col-12 @if ($language->default) active show @endif"
                                            id="language{{ $language->id }}">

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="col-12">
                                                        {{ Form::bsText('display_name[' . $language->id . ']', $action === 'create' ? old('display_name.' . $language->id) : $translations[$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="event_type">Tipo de evento</label>
                                                            <select class="selectpicker form-control form-control-sm"
                                                                name="event_type" id="event_type" data-actions-box="true"
                                                                data-live-search="true" disabled>

                                                                @foreach ($event_types as $item)
                                                                    @if ($event->event_type_id == $item->event_type_id)
                                                                        <option value="{{ $item->event_type_id }}" selected>
                                                                            {{ $item->display_name }}</option>
                                                                    @else
                                                                        <option value="{{ $item->event_type_id }}">
                                                                            {{ $item->display_name }}</option>
                                                                    @endif
                                                                @endforeach


                                                            </select>
                                                            <textarea style="transform: scale(0.0);display:none;height: 1px!important;" name="users_groups" id="users_groups">
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="roles">Cargos:</label>
                                                            <select class="selectpicker form-control form-control-sm" name="roles"
                                                                id="roles" data-actions-box="true" data-live-search="true"
                                                                multiple disabled>

                                                                
                                                                @foreach ($roles as $item)
                                                                    @if (in_array($item->role_id, $cargos))
                                                                        <option value="{{ $item->role_id }}" selected>
                                                                            {{ $item->display_name }}</option>
                                                                    @else
                                                                        <option value="{{ $item->role_id }}">
                                                                            {{ $item->display_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                                

                                                            </select>

                                                        </div>
                                                    </div>

                                                    {{-- Destinatários --}}

                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="users_group">Destinatário(s):</label>
                                                            <select class=" selectpicker form-control form-control-sm"
                                                                name="users_group" id="users_group" data-actions-box="true"
                                                                data-live-search="true" multiple disabled>
                                                                @foreach ($usuarios_cargos as $item)
                                                                    @if (in_array($item->id_usuario, $usuarios_id))
                                                                        <option value="{{ $item->id_usuario }}" selected>
                                                                            {{ $item->nome_usuario }} (
                                                                            {{ $item->email_usuario }}
                                                                            )</option>
                                                                    @else
                                                                        <option value="{{ $item->id_usuario }}">
                                                                            {{ $item->nome_usuario }} (
                                                                            {{ $item->email_usuario }}
                                                                            )</option>
                                                                    @endif
                                                                @endforeach

                                                            </select>
                                                            <textarea style="transform: scale(0.0);display:none;height: 1px!important;" name="users_groups" id="users_groups">
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">


                                                        <div class="row">

                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start', $event->start ?? '', ['type' => 'date', 'placeholder' => __('common.start_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.start_date')], ['class' => 'form-control col-6']) }}

                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end', $event->end ?? '', ['type' => 'date', 'placeholder' => __('common.end_date'), 'disabled' => $action === 'show', 'required', 'min' => date('Y-m-d')], ['label' => __('common.end_date')]) }}
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-12">


                                                        <div class="row">

                                                            <div class="col-6">
                                                                {{ Form::bsCustom('start_time', $event->start_time ?? '', ['type' => 'time', 'disabled' => $action === 'show'], ['label' => __('Hora de início')]) }}

                                                            </div>
                                                            <div class="col-6">

                                                                {{ Form::bsCustom('end_time', $event->end_time ?? '', ['type' => 'time', 'disabled' => $action === 'show'], ['label' => __('Hora de fim')]) }}
                                                            </div>

                                                            <div class="col-6">
                                                                <a href="{{ route('events.edit', $event->id) }}"
                                                                    class="btn ml-3 btn-warning mb-3">
                                                                    @icon('fas fa-edit')
                                                                    @lang('common.edit')
                                                                </a>
                                                            </div>




                                                        </div>
                                                    </div>


                                                </div>
                                                <div class="col-6">
                                                    <div class="col-12">
                                                        <div class="form-group col">
                                                            <label for="description[' . $language->id . ']'">Descrição do
                                                                evento</label>
                                                            {{ Form::textarea('description[' . $language->id . ']', $action === 'create' ? old('description.' . $language->id) : $translations[$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                @break

                            @endswitch

                        </div>
                    </div>
                </div>



                {!! Form::close() !!}

            </div>


        </div>
    </div>

    </div>
    </div>






    </div>



@endsection
@section('scripts')









    @parent
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        $("textarea").attr('rows', '25');
        $("textarea[name='description[1]']").attr('id', 'description');
        $("textarea[name='description[1]']").addClass('form-control');

        CKEDITOR.replace('description');

        var valores = "";
        var destinatarios = $("#users_group");
        var cargos = $("#roles");

        function get_user(id_roles) {

            if (id_roles == "") {
                destinatarios.empty();
                destinatarios.attr('disabled', true);
                cargos.selectpicker('refresh');
                destinatarios.selectpicker('refresh');

            } else {



                $.ajax({
                    url: '/gestao-academica/ajax_users/' + id_roles,
                    type: "get",
                    data: $(this).serialize(),
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }
                    },
                    success: function(response) {
                        destinatarios.empty();
                        response.forEach(response => {
                            destinatarios.append("<option value='" + response["id_usuario"] + "-" + response["cargo_usuario"] + "'>" +
                                response["nome_usuario"] + " ( " + response["email_usuario"] +
                                " )</option>");
                        });
                        destinatarios.prop('disabled', false);
                        destinatarios.selectpicker('refresh');
                    }
                });

            }
        }

        $("#roles").on('change', function() {
            get_user($(this).val());
        });

        $("#users_group").on('change', function() {
            let usuarios = $(this).val();
            $("#users_groups").val(usuarios);
        });

        $(function() {
            $("#users_groups").val($("#users_group").val());
            });


        $(".submit").hover(
            function() {
                validar($(this), [$("#roles"), $("#users_group"), $("#event_type"), $(
                    "input[name='display_name[1]']"), $(
                    "input[name='start']"), $("input[name='end']"), $("input[name='start_time']"), $(
                    "input[name='end_time']")]);
            }
        );


        // Função para validar dados dados para a criação dos eventos

        function validar(botao, elementos) {
            elementos.forEach(elemento => {

                if ((elemento.val() == " " || elemento.val() == "" || elemento.val() == "undefined" || elemento
                        .val() == null)) {

                    habilitar(0, botao, elemento);
                } else {
                    habilitar(1, botao, elemento);
                }

            });
        }

        function habilitar(valor, botao, elemento) {

            switch (valor) {
                case 0:
                    // elemento.removeClass("is-valid");
                    elemento.addClass("is-invalid");
                    $(".submit").hide();
                    // botao.prop('disabled', true);

                    break;
                case 1:
                    elemento.removeClass("is-invalid");
                    // elemento.addClass("is-valid");
                    // botao.prop('disabled', false);
                    break;

                default:
                    break;
            }

        }
        $("label").hover(
            function() {
                $(".submit").show();
            }
        );

        $("input[name='end_time'],input[name='start_time'],input[name='end'],input[name='start']").on('change',
            function() {

                var data_inicio = $("input[name='start']");
                var data_fim = $("input[name='end']");
                var hora_inicio = $("input[name='start_time']");
                var hora_fim = $("input[name='end_time']");
                var x = "";


                if ((data_inicio.val() > data_fim.val()) && (data_fim.val() != "")) {

                    x = data_inicio.val();
                    data_inicio.val(data_fim.val());
                    data_fim.val(x);

                }

                if ((data_inicio.val() == data_fim.val()) && (hora_inicio.val() > hora_fim.val()) && (hora_fim.val() !=
                        "")) {

                    x = hora_inicio.val();
                    hora_inicio.val(hora_fim.val());
                    hora_fim.val(x);

                }





            }
        );
    </script>

@endsection
