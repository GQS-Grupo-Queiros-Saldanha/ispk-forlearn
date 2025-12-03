 
<a href="/pt/users/cards/edit/{{$item->matriculation_id}}/{{$item->id_anoLectivo}}" class="btn btn-warning"> 
    <center><i class="fa fa-edit text-black"></i></center>
</a>
@php
    $allowed = [2,1425];
    if(auth()->check() && in_array(auth()->id(), $allowed)){
        $link = "/pt/users/cards/student/pdf/".$item->id.",1";    
    }else{
        $link = "#";
    }
@endphp
<a href="{{$link}}" target="_blank" class="btn btn-success"> 
    <center><i class="fas fa-address-card"></i></i></center>
</a>


