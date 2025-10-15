@php 
    use App\Modules\Users\Controllers\CandidatesController;
    $cursos = CandidatesController::get_course($cadidate->id);
@endphp

{{implode(",",$cursos)}}  