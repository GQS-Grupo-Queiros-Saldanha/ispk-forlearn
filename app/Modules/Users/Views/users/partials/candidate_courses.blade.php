 @if (isset($hidden))

 @else
     @php($currentUserIsAuthorized = auth()->user()->hasAnyRole(['candidado-a-estudante']))
     <div class="card">
         <div class="card-body row">
             @if ($user->hasAnyRole(['teacher', 'candidado-a-estudante']))
                 <div class="col col-4">
                     <h5 class="card-title mb-3">@lang('Users::users.courses')</h5>
                     @if ($action !== 'show' && $currentUserIsAuthorized)
                         {{ Form::bsLiveSelect('course[]', $courses, $action === 'create' ? old('courses') : $user->courses->pluck('id'), ['multiple', 'id' => 'course']) }}
                     @else
                         {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                     @endcan
             </div>
         @endif
         @if ($user->hasAnyRole(['candidado-a-estudante', 'teacher']))
             <div class="col col-4">
                 <h5 class="card-title mb-3">@lang('Users::users.disciplines')</h5>
                 @if ($action !== 'show' && $currentUserIsAuthorized)
                     {{ Form::bsLiveSelectEmpty('disciplines[]', [], null, ['multiple', 'id' => 'disciplines', 'disabled']) }}
                 @else
                     {!! implode(', ', $user->disciplines->pluck('currentTranslation.display_name')->toArray()) !!}
                 @endif
             </div>
         @endif
         @if ($user->hasRole(['candidado-a-estudante', 'teacher','superadmin']))
             <div class="col col-4">
                 <h5 class="card-title mb-3">@lang('GA::classes.class')</h5>
                 @if ($action !== 'show' && $currentUserIsAuthorized)
                     {{ Form::bsLiveSelectEmpty('classes[]', [], null, ['multiple', 'id' => 'classes', 'disabled']) }}
                 @else
                     {!! implode(', ', $user->classes->pluck('display_name')->toArray()) !!}
                 @endif
             </div>
         @endif
     </div>
 </div>
@endif
{{-- @if ($user->hasRole('candidado-a-estudante') && $currentUserIsAuthorized)
    <div class="card">
        <div class="card-body">
            <h5 class="mt-5 text-uppercase">@lang('Users::users.candidates')</h5>
            <div class="row">
                <div class="col col-sm-6">
                    @if ($currentUserIsAuthorized)
                        {{ Form::bsText('n_cadidate', $user->candidate ? $user->candidate->code : null , ['disabled' => (bool) $user->candidate, 'required'], ['label' => __('Users::users.candidate_number')]) }}
                    @elseif($user->candidate)
                        {!! __('Users::users.candidate_number') . ': ' . $user->candidate->code !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif --}}
