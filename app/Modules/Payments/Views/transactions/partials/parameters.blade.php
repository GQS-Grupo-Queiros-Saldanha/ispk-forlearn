<div class="card">
    <div class="card-body">
        @foreach($parameter_groups as $parameter_group)
            @if($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))
                <h5 class="mt-5">{{ $parameter_group->currentTranslation->display_name }}</h5>
                <div class="row">
                    @foreach($parameter_group->parameters as $parameter)
                        <div class="col col-sm-6">
                            @include('Users::users.partials.parameter', ['parameter' => $parameter, 'action' => $action, 'parameter_group' => $parameter_group, 'user' => $user])
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>

@section('scripts')
@parent
<script>
    $(function () {
        $('select[data-options-have-related-parameters]').on('change', function () {

            var $self = $(this);

            // Esconder todas os parâmetros relacionados a qualquer opção
            var $container = $self.parent().find('[data-parameter]');
            $container.collapse('hide');

            // Limpar e desativar todos os parâmetros relacionados
            $container.find('input, textarea, select').prop('disabled', true);

            // Obter da opção selecionada os parâmetros relacionados
            var $option = $("option:selected", this);
            var relatedParameters = $option.attr('data-related-parameters');
            if (typeof relatedParameters !== 'undefined' && relatedParameters.length > 0) {

                relatedParameters = JSON.parse(relatedParameters);

                // Ativar e mostrar os parâmetros relacionados da opção selecionada
                $.each(relatedParameters, function (k, v) {
                    var $relatedParameterContainer = $self.parent().find('[data-parameter="' + v + '"]');
                    if ($relatedParameterContainer.length) {
                        $relatedParameterContainer.find('input, textarea, select').prop('disabled', false);
                        $relatedParameterContainer.collapse('show');
                    }
                });
            }

        }).trigger('change');
    });
</script>
@endsection
