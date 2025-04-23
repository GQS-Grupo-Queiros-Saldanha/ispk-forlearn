
{{-- Se o estado atual for mudança de curso, exibir botão para voltar ao ativo (frequentar)--}}
@if ($item->states_id == 9)
    @if ($item->course_id == $item->id_course)
        <a href="{{ route('users.edit', $item->id) }}" target="_blank" class="btn btn-success btn-sm" onclick="return confirm('Deseja mudar o curso?');">
            Mudar o curso
        </a>
    @else
        <a href="{{ route('matriculations.edit', $item->matriculation_id) }}" target="_blank" class="btn btn-success btn-sm" onclick="return confirm('Deseja continuar?');">
            Editar matrícula
        </a>
    @endif

{{-- Se o estado atual for em aprovacao de transferencia--}}
@elseif($item->states_id == 14)
    @if($item->matriculation_id == null)
        <a href="{{ route('matriculations.create')}}" target="_blank" class="btn btn-success btn-sm" onclick="return confirm('Deseja efetuar matrícula?');">
            Efetuar matrícula
       </a>
    @elseif($item->matriculation_id == !null)
        <a href="{{ route('old_student.add', $item->id)}}" target="_blank" class="btn btn-success btn-sm" onclick="return confirm('Deseja efetuar lançamento da notas por equivalência?');">
            Lançar notas    
       </a>
    @endif
@endif

<!--  -->

@section('scripts')
    @parent
    <script>
      $(function(){
        console.log("Ola!");
      })
    </script>
      @endsection

