{{-- {{$item->description}} --}}

@php    
    
    echo preg_replace("/</", ' ', $item->description);

@endphp

