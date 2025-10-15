<table class="table table-parameter-group"> 
    <thead class="thead-parameter-group">
        
        <th class="th-parameter-group">IDENTIFICAÇÃO NO ISPM</th>
    
    </thead>
    </table >
    <table class="table table-parameter-group" width="100%">
        <thead>
        <th style="font-size: 8pt;" >Matrícula | Mecanográfico</th>
        <th style="font-size: 8pt;">Nome </th>
        <th style="font-size: 8pt;">Curso </th>
        <th style="font-size: 8pt;">Turma </th>
        <th style="font-size: 8pt;">Sala </th>
        </thead>

        <tbody class="tbody-parameter-group">
           <tr> 
                <td class="td-parameter-column" style="font-size: 8pt;">
                   @foreach($user->courses as $course) {{ $course->currentTranslation->display_name }} <br>@endforeach
            </td>

            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            </tr>
        </tbody>
    </table>
<br>

    <table class="table table-parameter-group"> 
    <thead class="thead-parameter-group">
        <th class="th-parameter-group">DADOS DO RECIBO</th>
    </thead>
        <tbody class="tbody-parameter-group">
           <tr> 
                <td class="td-parameter-column" style="font-size: 8pt;">Recebemos a quantia de ###### AKZ referente a: #######</td>
            </tr>
        </tbody>
    </table>

    <br>


<table class="table table-parameter-group"> 
    <thead class="thead-parameter-group">
        
        <th class="th-parameter-group">MODO DE PAGAMENTO</th>
    
    </thead>
    </table >
    <table class="table table-parameter-group" width="100%">
        <thead>
        <th style="font-size: 8pt;" >Data: ######</th>
        <th style="font-size: 8pt;">Nome </th>
        <th style="font-size: 8pt;">Curso </th>
        <th style="font-size: 8pt;">Turma </th>
        <th style="font-size: 8pt;">Sala </th>
        </thead>

        <tbody class="tbody-parameter-group">
           <tr> 
                <td class="td-parameter-column" style="font-size: 8pt;">
                   @foreach($user->courses as $course) {{ $course->currentTranslation->display_name }} <br>@endforeach
            </td>

            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            <td class="td-parameter-column" style="font-size: 8pt;" >@foreach($user->disciplines as $discipline) {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name}}<br> @endforeach</td>
            </tr>
        </tbody>
    </table>
 
<br>

@foreach($parameter_groups as $parameter_group)
    @if($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))

        @php
            $parameters = $parameter_group->parameters->filter(function($item) {
            return !in_array($item->type, ['file_pdf', 'file_doc'], true) && $item->code !== 'fotografia';
        });
        @endphp

        @if(count($parameters) > 0)
            <table class="table table-parameter-group">
                <thead class="thead-parameter-group">
                <tr>
                    <th class="th-parameter-group" colspan="{{ min($options['columns_per_group'], count($parameters)) }}"> {{ $parameter_group->currentTranslation->display_name }}</th>
                </tr>
                </thead>
                <tbody class="tbody-parameter-group">
                <tr>
                    @foreach($parameters->chunk(ceil($parameters->count() / $options['columns_per_group'])) as $chunk)
                        <td class="td-parameter-column">
                            @foreach($chunk as $parameter)
                                @include('Users::users.partials.pdf_parameter', ['parameter' => $parameter, 'action' => $action, 'parameter_group' => $parameter_group, 'user' => $user])
                            @endforeach
                        </td>
                    @endforeach
                </tr>
                </tbody>
            </table>
        @endif
    @endif
@endforeach

@section('scripts')
    @parent
    <script>
        (function() {
            var $selects = document.querySelectorAll('select[data-options-have-related-parameters]');
            for (var i = 0; i < $selects.length; i++) {
                $selects[i].addEventListener("change", function() {

                    // Esconder todas os parâmetros relacionados a qualquer opção
                    var $containers = this.parentNode.querySelectorAll('[data-parameter]');
                    for (var i = 0; i < $containers.length; i++) {
                        $containers[i].classList.add('collapse');
                    }

                    // Obter da opção selecionada os parâmetros relacionados
                    var $option = this.options[this.selectedIndex];
                    var relatedParameters = $option.getAttribute('data-related-parameters');
                    if (typeof relatedParameters !== 'undefined' && relatedParameters !== null && relatedParameters.length > 0) {
                        relatedParameters = JSON.parse(relatedParameters);

                        // Ativar e mostrar os parâmetros relacionados da opção selecionada
                        for (var i = 0; i < relatedParameters.length; i++) {
                            var $relatedParameterContainer = this.parentNode.querySelector('[data-parameter="' + relatedParameters[i] + '"]');
                            if ($relatedParameterContainer !== null) {
                                $relatedParameterContainer.classList.remove('collapse');
                            }
                        }
                    }
                });

                if ("createEvent" in document) {
                    var evt = document.createEvent("HTMLEvents");
                    evt.initEvent("change", false, true);
                    $selects[i].dispatchEvent(evt);
                } else {
                    $selects[i].fireEvent("onchange");
                }
            }
        })();
    </script>
@endsection
