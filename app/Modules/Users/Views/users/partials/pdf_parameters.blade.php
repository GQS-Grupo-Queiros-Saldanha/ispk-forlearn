{{-- DESABILITADO POR MOTIVOS ESPECIAS @$$@QUI --}}
   
<style>
    *{
        border:none!important;
        box-shadow:none!important;
    }
.form-group>*,.table-parameter-group>*{
    font-size: @php echo $options['font-size'] @endphp !important;
}
li{
    list-style: none!important;
}
</style>
@php    
    $i_count=0;
@endphp
   @if (!empty($user->roles))
       @if (substr($user->roles->first()->currentTranslation->display_name, -8) == 'studante')
      
           <table class="table table-parameter-group  " width="100%">
               <thead class="bg1">
                <tr class=""><td style="background-color:white;color:white;font-size:5px;">s</td></tr>
                @if (substr($user->roles->first()->currentTranslation->display_name, -8) == 'studante')
                   <th style="text-indent:0.5em;" colspan="4"> CANDIDATURA</th>
                @else
                   <th style="text-indent:0.5em;" colspan="4"> DADOS ACADÉMICO</th>
                @endif
                <tr class=""><td style="background-color:white;color:white;font-size:5px;">s</td></tr>
               </thead>
               
               <tbody class="tbody-parameter-group">
                   <tr>
                 
                           <td class="td-parameter-column" ><b>Curso</b> <br>
                               @isset($user_fase->curso)
                                   {{ $user_fase->curso }}
                               @else
                                   @foreach ($user->courses as $course)
                                       {{ $course->currentTranslation->display_name }}
                                   @endforeach
                               @endisset
                           </td>
                 

                       <td class="td-parameter-column" ><b>Disciplina</b> <br>
                           @isset($user_fase->turma)
                           @else
                               @foreach ($user->disciplines as $discipline)
                                 <li>  {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name }}</li>
                               @endforeach
                           @endisset
                       </td>
                       <td ><b>Turma</b> <br>
                           @isset($user_fase->turma)
                               {{ $user_fase->turma }}
                           @else
                               @foreach ($user->classes as $classe)
                               <li> {{ $classe->display_name }} </li>
                               @endforeach
                           @endisset


                       <td ><b>Sala</b> <br>
                           @isset($user_fase->sala)
                               {{ $user_fase->sala }}
                           @else
                               @foreach ($user->classes as $room)
                               <li>  {{ $room->room->currentTranslation->display_name }} </li>
                               @endforeach
                           @endisset
                       </td>
                   </tr>
               </tbody>
           </table>
         
       @endif
   @endif
   @foreach ($parameter_groups as $parameter_group)
       @if ($user->hasAnyRole($parameter_group->roles->pluck('id')->toArray()))
           @php
               $parameters = $parameter_group->parameters->filter(function ($item) {
                   return !in_array($item->type, ['file_pdf', 'file_doc'], true) && $item->code !== 'fotografia';
               });
           @endphp

           @if (count($parameters) > 0)
               <table class="table table-parameter-group">
                   <thead class="thead-parameter-group">
                       
                       <tr class="bg1">
                           @if ($parameter_group->id == 1)
                               <th class="th-parameter-group" style="font-size: {{$options['font-size']}}"
                                   colspan="{{ min($options['columns_per_group'], count($parameters) + 1) }}">
                                   {{ $parameter_group->currentTranslation->display_name }}</th> 
                           @else
                               <th class="th-parameter-group" style="font-size: {{$options['font-size']}}"
                                   colspan="{{ min($options['columns_per_group'], count($parameters)) }}">
                                   {{ $parameter_group->currentTranslation->display_name }}</th> 
                           @endif
                           
                       </tr>
                       <tr class=""><td style="background-color:white;color:white;font-size:5px;">s</td></tr>
                   </thead>
                   <tbody class="tbody-parameter-group">
                       <tr>
                           @foreach ($parameters->chunk(ceil($parameters->count() / $options['columns_per_group'])) as $chunk)
                               <td class="td-parameter-column"  >
                                   @foreach ($chunk as $parameter)
                                       @include('Users::users.partials.pdf_parameter', [
                                           'parameter' => $parameter,
                                           'action' => $action,
                                           'parameter_group' => $parameter_group,
                                           'user' => $user,
                                       ])
                                   @endforeach
                                   
                               </td>
                           @endforeach
                       </tr>
                   </tbody>
               </table>
           @endif
       @endif
   @endforeach

            
        <div class="page-break"></div>
    
       @if (!empty($user->roles))
       
       @if (substr($user->roles->first()->currentTranslation->display_name, -6) == 'ocente')
            <div class="page-break" style="page-break-before: always!important"></div>
           <table class="table table-parameter-group  " width="100%;">
               <thead class="bg1">
                <tr class=""><td style="background-color:white;color:white;font-size:5px;">s</td></tr>
                @if (substr($user->roles->first()->currentTranslation->display_name, -8) == 'studante')
                   <th style="text-indent:0.5em;" colspan="4"> CANDIDATURA</th>
                @else
                   <th style="text-indent:0.5em;" colspan="4"> DADOS ACADÉMICO</th>
                @endif
                <tr class=""><td style="background-color:white;color:white;font-size:5px;">s</td></tr>
               </thead>
               
               <tbody class="tbody-parameter-group">
                    <tr>
                    <td class="td-parameter-column" ><b>Departamento</b> <br>
                        @isset($Departamento)
                            {{ $Departamento }}
                        @endisset
                    </td>
                    </tr>
                    <tr>
                        <td class="td-parameter-column" ><br>
                            
                        </td>
                    </tr>
                   <tr>
                 
                           <td class="td-parameter-column" ><b>Curso</b> <br>
                               @isset($user_fase->curso)
                                   {{ $user_fase->curso }}
                               @else
                                   @foreach ($user->courses as $course)
                                       {{ $course->currentTranslation->display_name }}
                                   @endforeach
                               @endisset
                           </td>
                 
    
                       <td class="td-parameter-column" ><b>Disciplina</b> <br>
                           @isset($user_fase->turma)
                           @else
                               @foreach ($user->disciplines as $discipline)
                                 <li>  {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name }}</li>
                               @endforeach
                           @endisset
                       </td>
                       <td ><b>Turma</b> <br>
                           @isset($user_fase->turma)
                               {{ $user_fase->turma }}
                           @else
                               @foreach ($user->classes as $classe)
                               <li> {{ $classe->display_name }} </li>
                               @endforeach
                           @endisset 
    
    
                       <td ><b>Sala</b> <br>
                           @isset($user_fase->sala)
                               {{ $user_fase->sala }}
                           @else
                               @foreach ($user->classes as $room)
                               <li>  {{ $room->room->currentTranslation->display_name }} </li>
                               @endforeach
                           @endisset
                       </td>
                   </tr>
               </tbody>
           </table>
         
       @endif
    @endif
        


       <table class="table-borderless" style="margin-left:4px;margin-top:54px">
           <thead style="text-align:left;">
          
           </thead>
           <tbody>
              
         
               <tr>
            @if (substr($user->roles->first()->currentTranslation->display_name, -11) == 'a estudante')
                <td style="font-size: {{$options['font-size']}};"><b style="margin-bottom: 20px;">Candidato(a) a estudante</b><br><br>
            @else
                <td style="font-size: {{$options['font-size']}};"><b >
                    @if(!empty($user->roles))
                    {{ $user->roles->first()->currentTranslation->display_name }}
                    @endif
                    </b>
                    <br><br>
            @endif
                       __________________________________________________________________<br>
                       {{ $user->name }}

                       

                   </td>

                   <td style="color: white;">_____</td>
                   

                   <td style="font-size: {{$options['font-size']}};"><b>Staff da IE</b><br><br>

                       __________________________________________________________________<br>
                       {{ $Funcionario->name }}

                       

                   </td>

               </tr>

           </tbody>
       </table>


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
                       if (typeof relatedParameters !== 'undefined' && relatedParameters !== null &&
                           relatedParameters.length > 0) {
                           relatedParameters = JSON.parse(relatedParameters);

                           // Ativar e mostrar os parâmetros relacionados da opção selecionada
                           for (var i = 0; i < relatedParameters.length; i++) {
                               var $relatedParameterContainer = this.parentNode.querySelector('[data-parameter="' +
                                   relatedParameters[i] + '"]');
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
