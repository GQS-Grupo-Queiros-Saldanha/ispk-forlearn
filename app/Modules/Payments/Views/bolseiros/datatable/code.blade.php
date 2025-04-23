@php use App\Modules\Payments\Controllers\BolseirosController; @endphp
@if (isset($item->code) && isset($item->year))    
    {{ BolseirosController::get_code_doc($item->code,$item->year) }}
@endif


