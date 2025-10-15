<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

@php use App\Modules\Users\util\MatriculationUtil;  @endphp
@php $bloqueado = MatriculationUtil::verificarAnoCurricularBloqueado($item); @endphp

<a href="{{ route('matriculations.show', $item->id) }}" class="btn btn-info btn-sm">
    <i class="far fa-eye"></i>
</a>
<a href="{{ route('matriculations.report', $item->id) }}"
    target="_blank"
    class="btn btn-sm btn-info">
     <i class="fas fa-file-pdf"></i>
 </a>
@if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) || auth()->user()->hasAnyPermission(['editar_matricula']) )
    <a href="{{ route('matriculations.edit', $item->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
@endif

<!--@if(auth()->user()->hasAnyRole(['superadmin' ]))-->
<!--<button class="btn btn-sm btn-danger" data-toggle="modal" data-type="delete" data-target="#modal_confirm"-->
<!--      data-action="{{ json_encode(['route' => ['matriculations.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"-->
<!--    type="submit">-->
<!--    <i class="fas fa-trash-alt"></i>-->
<!--</button>-->
<!--@endif-->

@if(auth()->user()->hasAnyPermission(['tesouraria_ver_menu' ]))
<a target="_blank" class="btn btn-sm  btn-info" href="{{ route('user_requests', $item->id) }}">
    <i class="fa-solid fa-t"></i>
</a>
@endif

@if( auth()->user()->hasAnyPermission(['manage-users']))
<a target="_blank" class="btn btn-sm  btn-warning" href="{{ route('users.show',$item->id_usuario) }}">
    <i class="fa fa-user"></i>
</a>
@endif

@if(auth()->user()->hasAnyPermission(['av_gerir_percurso_academico' ]))
<a target="_blank" class="btn btn-sm  btn-info" href="{{ route('academic-path.percurso', $item->id_usuario) }}">
    <i class="fa-solid fa-p"></i>
</a>
@endif

@if ($bloqueado)
    {{-- sedrac lucas calupeteca--}}
    @if($item->course_year == 1)
        <button class="btn btn-sm btn-success btn-mudanca-curso" data-toggle="modal" data-target="#modalMudancaCurso"
        data-student="{{$item->student}}" data-matricula="{{$item->matricula}}" data-course="{{$item->course}}"
        data-courseyear="{{$item->course_year}}"
        
        data-userid="{{$item->user_id}}" 
        data-matriculaid="{{$item->id}}" 
        data-courseid="{{$item->id_course}}"
        data-lectiveid="{{$item->lective_year}}"
        title="Ano curricular bloqueiado - mudar a matrícula"
        >
            <i class="fas fa-exchange"></i>
        </button>
    @endif

@else
    @if (auth()->user()->hasAnyPermission(['Anular_matricula']) || auth()->user()->hasAnyRole(['superadmin' ]))
        <button title="Anualação de matrícula" data-user="{{$item->id}}"  data-classe="{{$item->classe}}" data-name="{{$item->student}}" data-code="{{$item->code_matricula}}" class="btn btn-sm btn-danger anular" data-toggle="modal" data-type="anular_matricula" data-target="#anulate_matricula" type="submit">
            <i class="fas fa-user-times"></i>
        </button>
          
    @endif
@endif




<script>


    
    $(".anular").click(function (e) { 

        var matricula_id = $("#matricula_id");
        var nome_completo = $("#nome_completo");
        var turma = $("#turma");
        var n_confirmacao = $("#n_confirmacao");
        $(".boxObservation").empty();

        var getUser=$(this).attr('data-user');
        var getName=$(this).attr('data-name');
        var getTurma=$(this).attr('data-classe');
        var getCode=$(this).attr('data-code');

        matricula_id.val(getUser);
        nome_completo.val(getName);
        n_confirmacao.val(getCode);
        turma.val(getTurma);

        // var getLectiveYear=$(this).attr('data-lective');
        var getLectiveYear = $("#lective_years")[0].selectedOptions[0].text;
        
        $("#nome").text(getName);                
        $("#n_mat").text(getCode);                
        $("#turmas").text(getTurma);
        $("#ano_lectivo").text(getLectiveYear);

    });

    
    //slc
    $('.btn-mudanca-curso').on('click',function(e){
        let objSelected = $(this);
        let selectorCursoMundanca = $('#m_selector_curso');

        $('#m_nome').val(objSelected.attr('data-student'));
        $('#m_matricula').val(objSelected.attr('data-matricula'));
        $('#m_curso').val(objSelected.attr('data-course'));
        $('#m_ano_curricular').val(objSelected.attr('data-courseyear'));

        $('#msg_curso').html(objSelected.attr('data-course'));
        $('#msg_ano_curricular').html(objSelected.attr('data-courseyear'));

        $('#m_id_user').val(objSelected.attr('data-userid'));
        $('#m_id_course').val(objSelected.attr('data-courseid'));
        $('#m_course_year').val(objSelected.attr('data-courseyear'));
        $('#m_num_matricula').val(objSelected.attr('data-matricula'));
        $('#m_id_matricula').val(objSelected.attr('data-matriculaid'));
        $('#m_id_lective_year').val(objSelected.attr('data-lectiveid'));

        $('#lective_years').children('option').each(function(i,item){
            if(item.value == objSelected.attr('data-lectiveid'))
                $('#msg_ano_lectivo').html(item.innerHTML.trim());
        });
        
        let isEmpty = selectorCursoMundanca.children('option').length == 0;

        if(isEmpty){
            let selectorCurso = $('#curso');
            if(selectorCurso){
                let html = '';
                let options = selectorCurso.children('option');
                options.each(function(i, item){
                    html += `<option value="${item.value}">${item.innerHTML}</option>`;
                });
                selectorCursoMundanca.html(html);
                selectorCursoMundanca.selectpicker();
            }
        }

    });

</script>