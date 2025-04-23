
@if($action === 'edit')
    <label for="main-role">Cargo Principal</label>
    <select name="main-role" id="main-role" class="form-control form-control-sm" data-live-search="true" data-actions-box="true">
            <option value="" selected></option>
         
        @foreach($user->roles as $role)
            <option value="{{$role->id}}" @if(isset($user_cargo) && ($user_cargo->value == $role->id)) selected @endif>{{$role->currentTranslation->display_name}}</option>
        @endforeach
    </select>
@endif
@if($action === 'show')
    <label for="main-role">Cargo Principal</label>
    {{ isset($user_cargo)  ? $user_cargo->display_name : 'N/A'}}
@endif

