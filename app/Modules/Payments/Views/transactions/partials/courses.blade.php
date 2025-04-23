<div class="card">
    <div class="card-body row">
        @if($user->hasAnyRole(['student', 'candidado-a-estudante']))
            <div class="col col-6">
                <h5 class="card-title mb-3">@lang('Users::users.course')</h5>
                @if($action !== 'show')
                    {{ Form::bsLiveSelect('course', $courses, $action === 'create' ? old('course') : $user->courses->pluck('id'), ['required']) }}
                @else
                    {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                @endcan
            </div>
        @endif
        @if($user->hasRole('teacher'))
            <div class="col col-6">
                <h5 class="card-title mb-3">@lang('Users::users.courses')</h5>
                @if($action !== 'show')
                    {{ Form::bsLiveSelect('courses[]', $courses, $action === 'create' ? old('courses') : $user->courses->pluck('id'), ['multiple', 'id' => 'courses']) }}
                @else
                    {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                @endcan
            </div>
            <div class="col col-6">
                <h5 class="card-title mb-3">@lang('Users::users.disciplines')</h5>
                @if($action !== 'show')
                    {{ Form::bsLiveSelectEmpty('disciplines[]', [], null, ['multiple', 'id' => 'disciplines', 'disabled']) }}
                @else
                    {!! implode(', ', $user->disciplines->pluck('currentTranslation.display_name')->toArray()) !!}
                @endcan
            </div>
        @endif
    </div>
</div>
