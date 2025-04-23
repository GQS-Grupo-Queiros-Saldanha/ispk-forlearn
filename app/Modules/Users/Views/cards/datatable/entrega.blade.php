@php use App\Modules\Users\Controllers\CardsController; @endphp
@php 
    $entrega = CardsController::verificar_cards($item->matriculation_id,$item->id_anoLectivo)  
    
    @endphp
    @if(isset($entrega->data_entrega))    
    {{$entrega->data_entrega}}
    @endif
