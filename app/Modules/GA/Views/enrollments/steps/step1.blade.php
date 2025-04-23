<h3>Aluno</h3>
<section>
    <h4>@lang('Users::users.user')</h4>
    {{ Form::bsLiveSelect('user', $users, $action === 'create' ? old('user') : $user->id, ['required']) }}

    <br><br>

    <h4>@lang('GA::access-types.access_type')</h4>
    {{ Form::bsLiveSelect('access_type', $access_types, $action === 'create' ? old('access_type') : $access_type->id, ['required']) }}
</section>
