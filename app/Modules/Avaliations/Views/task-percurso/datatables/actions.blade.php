
<style>
    form{
        display: contents;
    }
</style>

{!! Form::open(['route' => ['percurso_task.show'], 'method' => 'post', 'target' => '_blank']) !!}

 <input type="number" id="estudante" name="estudante" value="{{$item->codigo}}" class="d-none" />
 <input type="text" id="nome" name="nome" value="{{$item->nome}} ( {{$item->email}} )" class="d-none" />
 <input type="text" id="disciplina" name="disciplina" value="{{$percurso[$item->codigo]["disciplina"]}}" class="d-none" />

<button type="submit"tabindex="0" data-bs-toggle="tooltip" data-html="true" target="_blank"
    href="percurso_task/show" class="btn btn-info "
    style="    padding: 3px 6px 3px 6px;border-radius: 7px;font-size: 12px;">
    @icon('far fa-eye')
</button>
{!! Form::close() !!}


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