<div class="row">
    @if (in_array($action, ['show', 'edit']))
        <div class="col">
            @if (auth()->user()->hasRole('superadmin'))
                {{ Form::bsText('name', $user->name ?? null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e último nome', 'value' => $user->name ?? null ]) }}
            @else
                {{ Form::bsText('name', $user->name ?? null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'readonly', 'autocomplete' => 'name'], ['label' => __('Users::users.name'), 'value' => $user->name ?? null]) }}
            @endif

            @if (auth()->user()->hasRole(['superadmin', 'staff_forlearn']))
                {{ Form::bsEmail('email', $user->email ?? null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show' || isset($user->email) , 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email'), 'value' => $user->email ?? null ] ) }}
            @else
                {{ Form::bsEmail('email', $user->email ?? null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show' || isset($user->email), 'required', 'readonly', 'autocomplete' => 'email'], ['label' => __('Users::users.email'), 'value' => $user->email ?? null] ) }}
            @endif

            @if ($action === 'edit')
                {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
            @endif

            @if (!isset($hidden_cargo))
                @include('Users::users.partials.roles', ['large' => true])
            @endif

            @if (!isset($hidden_cargo) && auth()->user()->hasRole(['superadmin', 'staff_forlearn']))
          
          <div class="form-group col">
              @include('Users::users.partials.main-role')
          </div>

          
         @endif

            {{-- Code Márcia --}}
            @if ($action === 'edit')
                {{-- EDIT --}}
                @if (auth()->user()->hasAnyPermission(['gerir_grau_academico']) && !isset($hidden_cargo))
                    <div class="form-group col">
                        <label for="ga_type">Grau académico</label>
                        <select class="form-control form-control-sm" name="id_grau_academico" id="ga_type"
                            data-actions-box="true" data-live-search="true">
                            <option value="" selected></option>
                            @foreach ($graus_academicos as $ga)
                                @if( $userGA !== null && $userGA->id == $ga->id )
                                <option value={{ $ga->id }} selected>{{ $ga->nome }} </option>
                                @else
                                <option value={{ $ga->id }}>{{ $ga->nome }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif

                @if (auth()->user()->hasAnyPermission(['gerir_categorial_profissional']) && !isset($hidden_cargo))
                    <div class="form-group col">
                        <label for="ga_type">Categoria profissional</label>
                        <select class="form-control form-control-sm" name="id_categoria_profissional"
                            id="ga_type" data-actions-box="true" data-live-search="true">
                            <option value=""  selected></option>
                            @foreach ($categorias_profissionais as $cp)
                            @if( $userCP !== null  && $userCP->id == $cp->id )
                                <option value={{ $cp->id }} selected>{{ $cp->nome }} </option>
                            @else
                                <option value={{ $cp->id }}>{{ $cp->nome }} </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                @endif
            @endif


            @if ($action === 'show')
                @if (auth()->user()->hasAnyPermission(['gerir_grau_academico']) && !isset($hidden_cargo))
                    {{ Form::bsText('', $userGA->nome ?? null, ['disabled', 'required'], ['label' => 'Grau académico']) }}
                @endif
                
                @if (auth()->user()->hasAnyPermission(['gerir_categorial_profissional']) && !isset($hidden_cargo))
                    {{ Form::bsText('', $userCP->nome ?? null, [ 'disabled', 'required'], ['label' => 'Categoria profissional']) }}
                @endif
            @endif
        </div>
    @endif
    <div class="col">
        @if ($action === 'create')
            <div class=" pb-0">
                {{ Form::bsText('name', $user->name ?? null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.name'), 'value' => $user->name ?? null ]) }}
                {{ Form::bsEmail('email', $user->email ?? null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email'), 'value' => $user->email ?? null ]) }}
            </div>
            <div class="pt-0">
                {{ Form::bsText('full_name', null, ['placeholder' => 'Escreva apenas o primeiro e o último nome', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e último nome']) }}
                {{ Form::bsText('id_number', null, ['placeholder' => __('Users::users.id_number'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.id_number')]) }}
            </div>
            <div class="w pt-0" hidden id="confirmpassword">
                <div class="col-6"></div>
                {{ Form::bsText('confirm_password', null, ['placeholder' => 'Confirmar password', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => 'Confirmar password']) }}
            </div>
        @endif
        @if ($action !== 'create')
            @php
                $data = ['large' => true];
                if(isset($course_name) && !$user->hasRole('teacher') ) $data['course_name'] = $course_name;
            @endphp
            @include('Users::users.partials.courses', $data)        
        @endif
        @if ($action !== 'create')
            {{-- @include('Users::users.partials.scholarship-holder') --}}
        @endif
        @include('Users::users.partials.scholarship-holder')
      
            @include('Users::users.partials.regime_especial')
      
        <div class="pb-0">
            @if ($action !== 'create')
                @include('Users::users.partials.departments', ['large' => true])
            @endif
            @if ($action !== 'create')
                @include('Users::users.partials.coordinator', ['large' => true])
            @endif
            @if ($action !== 'create')
                @include('Users::users.partials.coordinator-special-course', ['large' => true])
            @endif
        </div>
    </div>
</div>
</div>