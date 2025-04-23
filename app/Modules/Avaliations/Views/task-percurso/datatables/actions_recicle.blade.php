<a class="btn btn-warning" style=" font-size: 10px;border-radius: 10px;" href="{{route('percurso_task.recicle',$item->codigo)}}"
     target="_blank">
@icon('fas fa-recycle') 
</a>  
<a class="btn btn-success" style=" font-size: 10px;border-radius: 7px;width: 34px;" 
    href="/users/matriculations/{{$item->id_matricula}}"
    title="Matrícula" target="_blank">
    @icon('fas fa-m') 
    </a>  

<a class="btn btn-info" style=" font-size: 10px;border-radius: 7px;width: 30px;" 
    href="/avaliations/percursoAcademico/academic-path/{{$item->codigo}}"
    title="Percurso" target="_blank">
    @icon('fas fa-p') 
</a>  