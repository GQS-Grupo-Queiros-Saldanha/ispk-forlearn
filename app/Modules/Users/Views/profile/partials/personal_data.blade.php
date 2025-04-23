<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Users::profile.personal_data')</h1>
                    @if (!auth()->user()->hasAnyRole(['candidado-a-estudante']))
                        <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-sm btn-warning">Editar perfil</a>
                    @endif
                </div>
                <div class="col-sm-6">
                    {{-- {{ Breadcrumbs::render('profile') }} --}}
                </div>
            </div>
        </div>
    </div>
<!--{{aguarda_matricula()}}-->
    {{-- Main content --}}
    <div class="content mt-5">
        <div class="container-fluid">
            @foreach($parameter_groups as $parameter_group)
                @php
                    $parameters = $user->parameters->filter(function($item) use ($parameter_group) {
                        return $item->pivot->parameter_group_id === $parameter_group->id && !in_array($item->type, ['file_doc','file_pdf','file_image']);
                    });
                @endphp

                @if(count($parameters) > 0)

                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5>{{ $parameter_group->currentTranslation->display_name }}</h5>
                        </div>

                        @foreach($parameters as $parameter)

                            <div class="col col-sm-3 form-group">
                                <label>{{ $parameter->currentTranslation->display_name }}</label>

                                @if(!in_array($parameter->pivot->value, [null,''], true))
                                    @if(in_array($parameter->type, ['dropdown', 'checkbox']))
                                        @php $value = ''; @endphp
                                        @php $option_ids = explode(',', $parameter->pivot->value); @endphp
                                        @foreach ($parameter->options as $option)
                                            @if(in_array($option->id, $option_ids))
                                                @php $value .= $option->currentTranslation->display_name . PHP_EOL; @endphp
                                            @endif
                                        @endforeach
                                        {{ $value }}
                                        {{--{!! Form::fText($parameter->currentTranslation->display_name, $value) !!}--}}
                                    @else
                                        {{ $parameter->pivot->value }}
                                        {{--{!! Form::fText($parameter->currentTranslation->display_name, $parameter->pivot->value) !!}--}}
                                    @endif
                                @else
                                    ...
                                @endif
                            </div>

                        @endforeach
                    </div>

                @endif
            @endforeach

        </div>
    </div>
</div>
