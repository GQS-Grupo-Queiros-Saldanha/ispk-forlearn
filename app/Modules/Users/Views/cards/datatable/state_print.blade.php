@php use App\Modules\Users\Controllers\CardsController; @endphp
@php 
    $impressao = CardsController::verificar_cards($item->matriculation_id,$item->id_anoLectivo)  
    
    @endphp
    @if(isset($impressao->data_impressao))    
    {{$impressao->data_impressao}}
    @endif

