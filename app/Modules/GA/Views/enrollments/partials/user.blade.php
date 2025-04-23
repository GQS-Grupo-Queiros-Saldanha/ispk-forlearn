<h2>Preencha os dados do aluno</h2>
<p>Preenchimento dos dados do aluno.</p>
@if(count($parameter_groups) > 0)
    {{-- {{ Form::bsText('number', null, ['placeholder' => __('GA::enrollments.number'), /*'disabled' => $action === 'show',*/ 'required'], ['label' => __('GA::enrollments.number')]) }} --}}
    @foreach($parameter_groups as $parameter_group_id => $parameters)

        @php
            // Get group details
            foreach ($user->parameters as $parameter) {
                $parameter_group = $parameter->groups->filter(function($item) use($parameter_group_id) {
                    return $item->id === $parameter_group_id;
                })->first();

                if($parameter_group) {
                    break;
                }
            };
        @endphp

        @if(!empty($parameter_group))
            <hr>
            <h3>{{ $parameter_group->currentTranslation->display_name}}</h3>
            <hr>
            @foreach($parameters as $parameter)
                @include('GA::enrollments.partials.user_parameter', [
                'action' => 'edit',
                'parameter_group' => $parameter_group,
                'parameter' => $parameter,
                'user' => $user,
                ])
            @endforeach
        @endif

    @endforeach
@else
    ...
@endif
