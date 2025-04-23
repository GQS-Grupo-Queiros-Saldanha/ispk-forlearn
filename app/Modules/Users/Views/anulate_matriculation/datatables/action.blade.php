@php 
use App\Modules\Avaliations\Controllers\RequerimentoController;
@endphp 




@if ($item->mode_anulate!=1)

      @php 
        $requerimento = RequerimentoController::get_requerimento($item->article);
        @endphp

    @if ($item->state == 'total' && $item->mode_anulate!=1)
        <center>
        {!! Form::open(['route' => ['document.generate-documentation'], 'method' => 'post', 'target' => '_blank']) !!}
        
        <input type="number" id="students" name="students" value="{{$item->user_id}}" class="d-none" />
        <input type="number" id="type_document" name="type_document" value="7" class="d-none" />
                @if(count($requerimento) > 0)
            <input type="number" id="requerimento" name="requerimento" value="{{$requerimento[0]->id}}" class="d-none" />
         @endif 
  
        <button class="btn btn-info btn-sm AnularBack" id="{{$item->id_anulate_matriculation}}" title="Recibo de anulação de matrícula" type="submit">
            <i class="fas fa-file-pdf"></i>
        </button>

        {!! Form::close() !!}
        </center>
    @endif
    
    
    @if($item->state != 'total')
        <center>
            <a href="{{route('anulate.matriculation.restaure',$item->id_anulate_matriculation)}}" class="btn btn-warning btn-sm AnularBack" id="{{$item->id_anulate_matriculation}}" title="Voltar matrícula anulada">
            <i class="fas fa-sync"></i>
            </a>
        </center>
    @endif
 
 @else
    <center>
        <a href="#" class="badge badge-warning" title="Anulação administrativa">administrativa</a>

        <a href="{{route('anulate.matriculation.restaure',$item->id_anulate_matriculation)}}" class="btn btn-warning btn-sm AnularBack" id="{{$item->id_anulate_matriculation}}" title="Voltar matrícula anulada">
            <i class="fas fa-sync"></i>
        </a>
   </center>
 @endif


