<ul class="nav nav-tabs m-2" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab"
            aria-controls="home" aria-selected="true">GERAL</button>
    </li>
    @if ($user->hasRole(['student', 'candidado-a-estudante']))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                role="tab" aria-controls="profile" aria-selected="true">CANDIDATURA</button>
        </li>
    @endif
    @foreach ($parameter_groups as $parameter_group)
        @if ($parameter_group->code == 'situacao_contratual' || $parameter_group->code == 'salarios_honorarios')
        @else
            @if ($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()) && !is_coordenador(auth()->user()))
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

    <li class="nav-item" role="presentation">
        @switch($action)
            @case('edit')
                <button type="submit" class="btn btn-primary mr-3" id="editUser">
                    @icon('fas fa-save')
                    @lang('common.save')
                </button>
            @break

            @case('create')
                <div hidden id="nextBtn">
                    @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
                        <button type="submit" class="btn btn-primary mb-3 mr-3">
                            @icon('fas fa-plus-circle')
                            @lang('common.create') docente
                        </button>
                    @endif
                </div>
            @break

        @endswitch
    </li>

    @if ($action === 'show')
        <li class="nav-item" role="">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning mr-3" style="margin-top: -12px;">
                <i class="fas fa-plus-square"></i>
                Editar formulário
            </a>
            @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
            <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal-pdf"
            onclick="$('#modal-pdf').modal()">
                <i class="fas fa-plus-square"></i>
                @php 
                     $codigo = $documentoCode_documento ?? 0;
                @endphp
                @if ($user->hasAnyRole(['student'])) 
                    Ficha Estudante 
                @elseif ($codigo == 5 || $user->hasAnyRole(['candidate']))
                    Ficha do CE 
                @elseif ($user->hasAnyRole(['superadmin']))
                    Ficha do Staff                    
                @else
                    Ficha do RH
                @endif               
            </a>
            @endif
            @if (auth()->user()->hasAnyRole(['superadmin']))
                @if ($user->hasAnyRole(['student']))
                    <a href="{{ route('cards.student', $user->id.","."1") }}" class="btn btn-success mr-3" style="margin-top: -12px;" target="_blank">
                        <i class="fas fa-address-card"></i>
                        Cartão de estudante
                    </a>
                 @endif 
            @endif
        </li>
    @endif
</ul>


<div class="tab-content mt-4" id="nav-tabContent">
    <div class="tab-pane fade show active position-relative default" id="home" role="tabpanel"
        aria-labelledby="home-tab">
        @if ($user->hasAnyRole(['student', 'candidado-a-estudante']))
            @include('Users::users.partials.user_general', [ 
                'hidden_cargo' => true, 'course_name' => 'course'
            ])
        @elseif($user->hasAnyRole([ 'teacher', 'candidado-a-estudante' ]))
            @include('Users::users.partials.user_general')
        @else
            @include('Users::users.partials.user_general',['course_name' => 'course'])
        @endif
    </div>
    @if ($user->hasAnyRole(['student', 'candidado-a-estudante']))
        <div class="tab-pane fade position-relative default" id="profile" role="tabpanel"
            aria-labelledby="profile-tab">
            <div class="w-50">
                @include('Users::candidate.partials.curso', [
                    'colNot' => true, 'course_name' => 'cursos'
                ])
            </div>
        </div>
    @endif
    @foreach ($parameter_groups as $parameter_group)
        @if ($parameter_group->code == 'situacao_contratual' || $parameter_group->code == 'salarios_honorarios')
        @else
        @if ($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()) && !is_coordenador(auth()->user()))
                <div class="tab-pane fade @if ($loop->index == 0)  @endif position-relative"
                    id="nav-tab-item-{{ $loop->index }}" role="tabpanel" aria-labelledby="nav-tab-{{ $loop->index }}"
                    tabindex="0">
                    <div class="row">
                        <div class="col col-sm-6">
                            @foreach ($parameter_group->parameters as $parameter)
                                @if (
                                        $parameter->id != 25 &&
                                        $parameter->id != 23 &&
                                        $parameter->id != 24 &&
                                        $parameter->id != 51 &&
                                        $parameter->id != 52 &&
                                        $parameter->id != 56 &&
                                        $parameter->id != 62 &&
                                        $parameter->id != 63 &&
                                        $parameter->id != 154 &&
                                        $parameter->id != 202 &&
                                        $parameter->id != 266 &&
                                        $parameter->id != 286 &&
                                        $parameter->id != 287 &&
                                        $parameter->id != 289 &&
                                        $parameter->id != 298)
                                    @include('Users::users.partials.parameter', [
                                        'parameter' => $parameter,
                                        'action' => $action,
                                        'parameter_group' => $parameter_group,
                                        'user' => $user,
                                    ])
                                @endif
                            @endforeach
                            {{-- @if ($parameter_group->id == 1 && $user->hasRole('student','candidado-a-estudante'))
                                @include('Users::candidate.partials.curso',['only_curso' => true, 'course_name' => "course_ints" ])
                            @endif --}}
                        </div>
                        <div class="col col-sm-6">
                            {{-- 52, 298 --}}
                            @php $parms = [23, 24, 25, 51, 52, 56,62, 63, 154, 266, 286, 287, 289, 277, 298]; @endphp
                            @foreach ($parameter_group->parameters as $parameter)
                                @if (in_array($parameter->id, $parms))
                                    @include('Users::users.partials.parameter', [
                                        'parameter' => $parameter,
                                        'action' => $action,
                                        'parameter_group' => $parameter_group,
                                        'user' => $user,
                                    ])
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
</div>

@section('scripts')
    @parent
    
    <script>
        $(function() {
            expandEscola();
            largeForm()

            function expandEscola() {
                let parm = $("[for='parameters[7][41]']");
                let parent = parm.parent();
                if (parent.length == 1 && parent[0].classList.contains('col')) {
                    parent[0].classList.remove('col');
                }
            }

            function largeForm() {
                let formControlSm = $('.large-form .form-control');
                formControlSm.each((index, item) => {
                    if (item.classList.contains("form-control-sm")) {
                        item.classList.remove("form-control-sm");
                    }
                })
            }

            $('[name="email"]').keyup(function(e) {
                $('input[name="parameters[11][312]"]').val($(this).val());
            })

            $('input[name="parameters[3][14]"]').keyup(function(e) {
                $('input[name="parameters[3][49]"]').val($(this).val());
            })

            $('input[name="parameters[13][14]"]').keyup(function(e) {
                $('input[name="parameters[13][49]"]').val($(this).val());
            })

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
