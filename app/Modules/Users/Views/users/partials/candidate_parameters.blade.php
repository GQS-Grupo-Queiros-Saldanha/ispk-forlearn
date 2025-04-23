<ul class="nav nav-tabs m-2" id="myTab" role="tablist">
    @if (!isset($candidatura_hidden))
        @if (!auth()->user()->hasAnyRole(['candidado-a-estudante']))
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="true">CANDIDATURA</button>
            </li>
        @endif
    @endif
    @foreach ($parameter_groups as $parameter_group)
        @if ($parameter_group->id != 5 && $parameter_group->id != 4)
            @if ($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))
                <li class="nav-item">
                    <a href="#" class="nav-link @if ($loop->index == 0)  @endif"
                        id="nav-tab-{{ $loop->index }}" data-toggle="tab"
                        data-target="#nav-tab-item-{{ $loop->index }}" type="button" role="tab"
                        aria-controls="nav-tab-item-{{ $loop->index }}" aria-selected="true">
                        {{ $parameter_group->currentTranslation->display_name }}
                    </a>
                </li>
            @endif
        @endif
    @endforeach
    @if ($action == 'edit' || $action == 'create')
        <li class="nav-item" role="presentation">
            <button type="submit" class="btn btn-primary mb-3 mr-3" id="editUser">
                @icon('fas fa-save')
                @lang('common.save')
            </button>
        </li>
    @endif
    @if ($action === 'show')
        <li class="nav-item" role="">
            @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas', 'staff_gestor_forlearn']))
            <a href="{{ route('candidates.edit', $user->id) }}" class="btn btn-warning mb-3 mr-3">
                <i class="fas fa-plus-square"></i>
                Editar
            </a>
            <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal-pdf"
                onclick="$('#modal-pdf').modal()">
                <i class="fas fa-plus-square"></i>
                Ficha do CE
            </a>
            @endif
        </li>
    @endif
</ul>
<div class="tab-content mt-4" id="nav-tabContent">
    @if (!isset($candidatura_hidden))
        @if (!auth()->user()->hasAnyRole(['candidado-a-estudante']))
            <div class="tab-pane fade show active position-relative default" id="profile" role="tabpanel"
                aria-labelledby="profile-tab">
                @include('Users::candidate.partials.curso')
            </div>
        @endif
    @endif
    @foreach ($parameter_groups as $parameter_group)
        @if ($parameter_group->id != 5 && $parameter_group->id != 4)
            @if ($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))
                <div class="tab-pane fade @if ($loop->index == 0)  @endif position-relative"
                    id="nav-tab-item-{{ $loop->index }}" role="tabpanel" aria-labelledby="nav-tab-{{ $loop->index }}"
                    tabindex="0">
                    <div class="row">
                        <div class="col">
                            @if ($parameter_group->id == 11)
                                @include('Users::candidate.partials.info')
                            @endif
                            @foreach ($parameter_group->parameters as $parameter)
                                @if (
                                    $parameter->id != 38 &&
                                        $parameter->id != 35 &&
                                        $parameter->id != 40 &&
                                        $parameter->id != 284 &&
                                        $parameter->id != 285 &&
                                        $parameter->id != 263 &&
                                        $parameter->id != 286 &&
                                        $parameter->id != 287 &&
                                        $parameter->id != 50 &&
                                        $parameter->id != 53 &&
                                        $parameter->id != 54 &&
                                        $parameter->id != 57 &&
                                        $parameter->id != 58 &&
                                        $parameter->id != 59 &&
                                        $parameter->id != 60 &&
                                        $parameter->id != 51 &&
                                        $parameter->id != 52 &&
                                        $parameter->id != 62 &&
                                        $parameter->id != 63 &&
                                        $parameter->id != 61 &&
                                        $parameter->id != 298 &&
                                        $parameter->id != 17 &&
                                        $parameter->id != 25 &&
                                        $parameter->id != 202 &&
                                        $parameter->id != 24 &&
                                        $parameter->id != 23)
                                    @include('Users::users.partials.parameter', [
                                        'parameter' => $parameter,
                                        'action' => $action,
                                        'parameter_group' => $parameter_group,
                                        'user' => $user,
                                        'col4' => true,
                                        'excludes' => [
                                            (object) ['group' => 13, 'parametor' => 17],
                                            (object) ['group' => 17, 'parametor' => 13],
                                            (object) ['group' => 13, 'parametor' => 56],
                                            (object) ['group' => 3, 'parametor' => 56],
                                            (object) ['group' => 2, 'parametor' => 154],
                                            (object) ['group' => 14, 'parametor' => 154],
                                            (object) ['group' => 12, 'parametor' => 202],
                                            (object) ['group' => 7, 'parametor' => 202],
                                        ],
                                    ])
                                @endif
                            @endforeach
                        </div>
                        <div class="col right">
                            @php $parms = [17, 23, 24, 25, 56, 154, 202]; @endphp
                            @foreach ($parameter_group->parameters as $parameter)
                                @php
                                    $data = [
                                        'parameter' => $parameter,
                                        'action' => $action,
                                        'parameter_group' => $parameter_group,
                                        'user' => $user,
                                    ];
                                @endphp
                                @if (in_array($parameter->id, $parms))
                                    @include('Users::users.partials.parameter', $data)
                                @endif
                            @endforeach
                            @php
                                $parm = (object) ['id' => 202, 'code' => 'diploma_ensino_médio_pdf', 'type' => 'file_pdf', 'has_options' => 0, 'required' => 1];
                                $data = [
                                    'parameter' => $parameter,
                                    'action' => $action,
                                    'parameter_group' => $parameter_group,
                                    'user' => $user,
                                ];
                            @endphp
                            @if ($parameter_group->id == 7)
                                @include('Users::users.partials.parameter', $data)
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
</div>

@section('scripts')
    @parent
    <script src="{{ asset('js/new_tabpane_form.js') }}"></script>
    <script>
        $(function() {
            $('select[data-options-have-related-parameters]').on('change', function() {

                var $self = $(this);

                // Esconder todas os parâmetros relacionados a qualquer opção
                var $container = $self.parent().find('[data-parameter]');
                $container.collapse('hide');

                // Limpar e desativar todos os parâmetros relacionados
                $container.find('input, textarea, select').prop('disabled', true);

                // Obter da opção selecionada os parâmetros relacionados
                var $option = $("option:selected", this);
                var relatedParameters = $option.attr('data-related-parameters');
                if (typeof relatedParameters !== 'undefined' && relatedParameters.length > 0) {

                    relatedParameters = JSON.parse(relatedParameters);

                    // Ativar e mostrar os parâmetros relacionados da opção selecionada
                    $.each(relatedParameters, function(k, v) {
                        var $relatedParameterContainer = $self.parent().find('[data-parameter="' +
                            v + '"]');
                        if ($relatedParameterContainer.length) {
                            $relatedParameterContainer.find('input, textarea, select').prop(
                                'disabled', false);
                            $relatedParameterContainer.collapse('show');
                        }
                    });
                }

            }).trigger('change');
        });
    </script>
@endsection
