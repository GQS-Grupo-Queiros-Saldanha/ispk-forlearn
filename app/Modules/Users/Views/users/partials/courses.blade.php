@php
    $currentUserIsAuthorized = auth()
        ->user()
        ->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas', 'coordenador-curso','rh_chefe','rh_assistente']);
@endphp

<div class="@if (!isset($large)) card @else large-form @endif">
  
    <div class="@if (!isset($large)) card-body row @else @endif">
        @if ($user->hasAnyRole(['student']))
            <div class="form-group col @if (!isset($large)) col-6 @endif">
                @if (isset($large))
                    <label for="">@lang('Users::users.course')</label>
                @else
                    <h5 class="card-title mb-3">@lang('Users::users.course')</h5>
                @endif
                @if ($action !== 'show' && $currentUserIsAuthorized)
                    <!--{{ Form::bsLiveSelect($course_name ?? 'course', $courses, null, ['placeholder' => '']) }}-->
                    @php 
                        $user_courses = $user->courses->map(function($q){ return $q->id;})->all();
                    @endphp
                    <select class="selectpicker form-control" id="{{ $course_name ?? 'course' }}" name="{{ $course_name ?? 'course' }}" tabindex="-98"
                        data-live-search="true" 
                        data-actions-box="false" 
                        data-selected-text-format="values">
                        @forEach($courses as $curso)
                            <option value="{{ $curso->id }}" @if(in_array($curso->id, $user_courses)) selected @endif >
                                {{ $curso->currentTranslation->display_name }}
                            </option>
                        @endforeach
                    </select>                    
                @else
                    {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                @endif
            </div>
           
        @endif
        @if ($user->hasAnyRole(['teacher', 'candidado-a-estudante']))
            <div class="@if (!isset($large)) col col-4 @else form-group col @endif">
                @if (isset($large))
                    <label class="">@lang('Users::users.courses')</label>
                @else
                    <h5 class="card-title mb-3">@lang('Users::users.courses')</h5>
                @endif
                @if ($action !== 'show' && $currentUserIsAuthorized)
                    {{ Form::bsLiveSelect($course_name ?? 'course[]', $courses, $action === 'create' ? old('courses') : $user->courses->pluck('id'), ['multiple', 'id' => $course_name ?? 'course']) }}
                @else
                    {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                @endcan
        </div>
    @endif
   
    @if ($user->hasAnyRole(['candidado-a-estudante', 'teacher']))
        <div class="@if (!isset($large)) col col-4 @else form-group col @endif">
            @if (isset($large))
                <label>@lang('Users::users.disciplines')</label>
            @else
                <h5 class="card-title mb-3">@lang('Users::users.disciplines')</h5>
            @endif
            @if ($action !== 'show' && $currentUserIsAuthorized)
                {{ Form::bsLiveSelectEmpty('disciplines[]', [], null, ['multiple', 'id' => 'disciplines', 'disabled']) }}
            @else
                {!! implode(', ', $user->disciplines->pluck('currentTranslation.display_name')->toArray()) !!}
            @endif
        </div>
    @endif
    @if ($user->hasRole(['candidado-a-estudante', 'teacher']))
        <div class="@if (!isset($large)) col col-4 @else form-group col @endif">
            @if (isset($large))
                <label>@lang('GA::classes.class')</label>
            @else
                <h5 class="card-title mb-3">@lang('GA::classes.class') gg</h5>
            @endif
            @if ($action !== 'show' && $currentUserIsAuthorized)
                {{ Form::bsLiveSelectEmpty('classes[]', [], null, ['multiple', 'id' => 'classes', 'disabled']) }}
            @else
                {!! implode(', ', $user->classes->pluck('display_name')->toArray()) !!}
            @endif
           
        </div>
       
    @endif
  
</div>
