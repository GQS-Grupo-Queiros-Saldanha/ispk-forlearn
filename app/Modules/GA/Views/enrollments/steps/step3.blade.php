<h3>Cursos</h3>
<section>
    <h2>Escolha o curso a que o aluno se vai inscrever</h2>
    {{--<table id="table-study-plan-editions" class="table table-striped">
        <thead>
        <tr>
            <th>@lang('GA::study-plan-editions.study_plan_editions')</th>
        </tr>
        </thead>
        <tbody>--}}
        @if(count($study_plan_editions)>0)
            {{--@foreach($study_plan_editions as $study_plan_edition)--}}
                {{ Form::bsLiveSelect('study_plan_edition', $study_plan_editions, $action === 'create' ? old('study_plan_edition') : null, ['required']) }}
                {{--<tr>
                    <td>
                        {{ Form::bsCheckbox('study_plan_editions[]', $study_plan_edition->id, null, ['disabled' => $action === 'show'], ['label' => $study_plan_edition->currentTranslation->display_name]) }}
                    </td>
                </tr>--}}
           {{-- @endforeach--}}
        @else
            Ainda não existem "cursos"
        @endif{{--
        </tbody>
    </table>--}}
</section>
