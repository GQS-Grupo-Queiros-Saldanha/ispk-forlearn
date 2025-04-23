<div class="">
    @if (in_array($action, ['show', 'edit']))
        <div class="">
            @if (auth()->user()->hasRole('candidado-a-estudante'))
                {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'readonly', 'required', 'autocomplete' => 'name', 'readonly'], ['label' => 'Primeiro e o último nome']) }}
                {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'readonly', 'required', 'autocomplete' => 'email', 'readonly'], ['label' => __('Users::users.email')]) }}
            @else
                {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'readonly', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e o último nome']) }}
                {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'readonly', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
            @endif

            @if ($action === 'edit')
                {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
            @endif
        </div>
    @endif
    @if ($action === 'create')
        <div class="">
            {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e o último nome']) }}
            {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
        </div>
        <div class="">
            {{ Form::bsText('full_name', null, ['placeholder' => __('Users::users.full_name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.full_name')]) }}
            {{ Form::bsText('id_number', null, ['placeholder' => __('Users::users.id_number'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.id_number')]) }}
        </div>
    @endif
    @if ($action !== 'create')
        @if (auth()->user()->hasAnyRole(['candidado-a-estudante']))
            @include('Users::users.partials.candidate_courses')
        @endif
    @endif
</div>
