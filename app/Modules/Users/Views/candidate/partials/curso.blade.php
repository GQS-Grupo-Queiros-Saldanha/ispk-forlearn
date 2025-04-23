@php
    use App\Modules\Users\util\CandidatesUtil;
@endphp
@if (!auth()->user()->hasAnyRole(['candidado-a-estudante']))
    @php
        $currentUserIsAuthorized = auth()
            ->user()
            ->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas', 'staff_matriculas', 'staff_gestor_forlearn']);
            $sizeCourse = sizeof($user->classes);
        $user_classes = $user->classes->map(function($q){ return $q->id;})->all();
    @endphp
    <div class="">
        <div class="form-group @if (!isset($colNot)) col @endif">
            <label class="">@lang('Users::users.courses')</label>
            @if ($action !== 'show' && $currentUserIsAuthorized)
                {{ Form::bsLiveSelect($course_name ?? 'course[]', $courses, $action === 'create' ? old($course_name ?? 'courses') : $user->courses->pluck('id'), ['multiple', 'id' => $course_name ?? 'course']) }}
            @else
                {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
            @endif
        </div>
        @if (!isset($only_curso))
            <div class="form-group @if (!isset($colNot)) col @endif" hidden>
                <label class="">@lang('Users::users.disciplines')</label>
                @if ($action !== 'show' && $currentUserIsAuthorized)
                    {{ Form::bsLiveSelectEmpty('disciplines[]', [], null, ['multiple', 'id' => 'disciplines', 'disabled']) }}
                @else
                    {!! implode(', ', $user->disciplines->pluck('currentTranslation.display_name')->toArray()) !!}
                @endif
            </div>
            <div class="form-group @if (!isset($colNot)) col @endif">
                <label class="">@lang('GA::classes.class')</label>
                @if( $sizeCourse != 0)
                    @if ($action !== 'show' && $currentUserIsAuthorized)
                        <select class="selectpicker form-control" data-live-search="true" name="classes[]" id="classes" multiple>
                            @foreach($user->classes as $class)
                                <option value="{{$class->id}}" @if(in_array($class->id, $user_classes)) selected @endif>
                                    {{ $class->display_name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        {!! implode(', ', $user->classes->pluck('display_name')->toArray()) !!}
                    @endif
                @else
                    @php $classes = CandidatesUtil::classCandidate($user); @endphp
                    <select class="selectpicker form-control" data-live-search="true" name="classes[]" id="classes">
                        <option>Seleciona uma turma</option>
                        @foreach($classes as $class)
                            <option value="{{$class->id}}" @if(CandidatesUtil::isUserClass($user->id, $class->id)) selected @endif>
                                {{ $class->display_name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endif
    </div>
@endif
