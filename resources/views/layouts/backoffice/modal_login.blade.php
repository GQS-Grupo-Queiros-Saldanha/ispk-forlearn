<div class="modal modal-forlean fade" tabindex="-1" role="dialog" id="modal-login">
    {!! Form::open(['url' => route('login'), 'class' => 'form-horizontal']) !!}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">@lang('auth.login')</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::bsEmail('email', old('email'), ['placeholder' => __('auth.email'), 'class' => 'input-forlearn', 'required', 'autocomplete' => 'email'], ['label' => false]) }}
                {{ Form::bsPassword('password', ['placeholder' => __('auth.password'), 'class' => 'input-forlearn', 'required', 'autocomplete' => 'current-password'], ['label' => false]) }}
                {{ Form::hidden('remember', false) }}

                <a class="btn btn-link text-dark small" href="{{ route('password.request') }}">
                    @lang('auth.forgot_password')
                </a>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-forlearn-dark">
                    @lang('auth.login')
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
