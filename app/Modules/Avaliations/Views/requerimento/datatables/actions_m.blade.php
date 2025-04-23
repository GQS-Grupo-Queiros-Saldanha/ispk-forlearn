{!! Form::open(['route' => ['document.generate-documentation'], 'method' => 'post', 'target' => '_blank']) !!}

<input type="number" id="students" name="students" value="{{$item->user_id}}" class="d-none" />
<input type="number" id="type_document" name="type_document" value="3" class="d-none" />
<input type="number" id="tipo" name="tipo" value="{{$item->tipo}}" class="d-none" />
<input type="number" id="student_year" name="student_year" value="{{$item->ano_curricular}}" class="d-none" />
<input type="number" id="lective_year" name="lective_year" value="{{$item->ano_lectivo}}" class="d-none" />
<input type="number" id="departamento" name="departamento" value="{{$item->departamento}}" class="d-none" />
<input type="text" id="seccao" name="seccao" value="{{$item->seccao}}" class="d-none" />


<button type="submit"tabindex="0" data-bs-toggle="tooltip" data-html="true" target="_blank"
    href="/reports/generate-declaration-note" class="btn btn-info "
    style="    padding: 3px 6px 3px 6px;border-radius: 7px;font-size: 12px;">
    <i class="fas fa-file-pdf"></i>
</button>
{!! Form::close() !!} 

<button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}" data-type="diploma" data-toggle="modal"
    data-target="#Modalconfirmar" type="submit">
    @icon('fas fa-trash-alt')
</button>