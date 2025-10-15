@php use App\Modules\Users\util\FaseCandidaturaUtil;  @endphp

@php $faseNext = FaseCandidaturaUtil::faseActual(); @endphp
@php $negativeInFase = FaseCandidaturaUtil::negativeInFase($item->id,$item->id_fase); @endphp

@php $existInNextFase = FaseCandidaturaUtil::existUserInFaseNext($item->id,$item->id_fase); @endphp
@php $existInHistorico = FaseCandidaturaUtil::existUserInHistorico($item->id,$item->id_fase); @endphp
@php $existTwoUserCources = FaseCandidaturaUtil::existTwoUserCources($item->id); @endphp
@php $url = route('users.generatePDF', $item->id); @endphp

<a href="{{ route('candidates.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye') 
</a>  

<a href="{{ route('candidates.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit') 
</a>

<button class="btn btn-sm btn-dark gerar-pdf" title="Ficha do CE" data-toggle="modal" data-target="#modal-pdf" onclick="$('#modal-pdf').modal()"  user="{{$item->id}}"
    url="{{$url}}" @if($existInHistorico) fase-exists="true" @endif>
    @icon('fas fa-file-pdf')
</button>
@if($negativeInFase && !$existInNextFase && $item->state == 'total' && isset($faseNext->fase))
    <button class="btn btn-sm btn-success btn-up-transicao" faseNext="{{$faseNext->fase}}" user="{{$item->id}}" fase="{{$item->id_fase}}">
        @icon('fas fa-arrow-up')
    </button>
@endif

@if($existInHistorico)
    <button class="btn btn-sm btn-info btn-historico" url="{{route('transferencia.historico',$item->id)}}" user="{{$item->id}}">
        @icon('fas fa-book')
    </button>
@endif

@if($existTwoUserCources && $item->state != 'total')
    <button class="btn btn-sm btn-warning btn-course" url="{{route('escolher.curso',$item->id)}}" user="{{$item->id}}">
        @icon('fas fa-bars')
    </button>
@endif

{{-- <a target="_blank" class="btn btn-sm  btn-info" href="{{ route('user_requests', $item->id) }}">
    <i class="fa-solid fa-t"></i>
</a> --}}

@if(auth()->user()->hasAnyPermission(['Apagar-candidatura']))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm"
            data-action="{{ json_encode(['route' => ['users.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}"
            type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif

{{-- <button class="btn btn-sm btn-success btn-pago-status btn-analisar" user="{{$item->id}}">
    @icon('fas fa-book')
</button> --}}

<script class="script-page">
    //var pagoStatus = $('.btn-pago-status');
    var gerarPDF = $('.gerar-pdf');
    var btnTransicaoFase = $('.btn-up-transicao');
    var btnHistorico = $('.btn-historico');
    var btnEscolherCurso = $('.btn-course');

    eliminarbtn();

    function eliminarbtn(){
        let scrips = $('.script-page');
        let tam = scrips.length;
        if(tam > 1)
            for(let i = 0; i <= tam-1; i++)
                $(scrips[i]).remove();
    }

  

    //sedrac codigo do historico candidatura
    
    var btnSubmit = $("#btn-submit");

    gerarPDF.on('click',function(e){
        let obj = $(this);
        let url = obj.attr('url');
        $('#user-id-model-pdf').val(obj.attr('user'));
        $('#fase_exists').val(obj.attr('fase-exists')? true : false);
        $('#form-modal-pdf').attr('action',url);
    });


    btnTransicaoFase.click(function(e){
        let objSelected = $(this);
        let fase = objSelected.attr('faseNext');
        let row =  objSelected.parent().parent().children();
        let nome = row[3].innerHTML;

        let modalFase = $('#modalFase');
        let ident = $('#modalFase #user');
        let form = $("#modalFase #form");
        let message = $('#modalFase #message');
        let formMethod = $("#modalFase [name='_method']");

        //message.html(`Tens certeza que dejesas fazer a transferência de fase do candidato (${nome}) para fase (${fase}) ?`);
        message.html(`Tem a certeza que pretende fazer a transferência ao candidato ?`);
        modalFase.modal('show');

        
        let faseNova = $("#modalFase #fase_nova");
        let lectiveCandidate = $('#modalFase #lective_candidate_id');

        //faseNova.val(fase);
        lectiveCandidate.val(objSelected.attr('fase'));

        ident.val(objSelected.attr('user'));
        form.attr('action', '{{ route('fase.candidatura.trans.user') }}');        
        formMethod.val('PUT');
    });

    btnHistorico.click(function(e){
        let obj = $(this);
        let url = obj.attr('url');
        let tbody = $('#tbody-historico');
        $('#user-pdf').val(obj.attr('user'));
        $.ajax({
            url: url,
            type: "GET",
            success: function(response){
               let html = "";
               response.forEach(item => {
                    html += `<tr>
                              <td>${item.ano_lectivo}</td>
                              <td>${item.curso}</td>
                              <td>${item.turma}</td>
                              <td>${item.fase}</td>
                              <td>
                                <button class="btn btn-sm btn-info" type="submit" name="lective_history_id" value="${item.id}">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                              </td>
                            </tr>`;
              });
              tbody.html(html);
              modalHistorico.modal('show');
            }
        })
    });

    btnEscolherCurso.click(function(e){
        let obj = $(this);
        let url = obj.attr('url');
        let tbody = $('#tbody-curso-escolher');
        let userId = $('#user_id_escolher');
        userId.val(obj.attr('user'));
        $.ajax({
            url: url,
            type: "GET",
            success: function(response){
               let html = "";
               response.forEach(item => {
                    html += `<tr>
                              <td>${item.display_name}</td>
                              <td>
                                <button class="btn btn-sm btn-info" type="submit" name="course_id" value="${item.courses_id}">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                              </td>
                            </tr>`;
              });
              tbody.html(html);
              modalEscolher.modal('show');
            }
        })
    });    

    aparecerSelectCurso();

    $("#caixaMudarCurso").change(function(){
        aparecerSelectCurso();
    });


    function aparecerSelectCurso(){
        if(!$("#caixaMudarCurso").prop('checked')){

        console.log("Mudar curso")
        $("#DivRemoveCurso").addClass('d-none');
        $("#validateCouse").val("NEWCOURSE");


        }else{

        $("#DivRemoveCurso").removeClass('d-none');
        $("#validateCouse").val("OLDCOURSE");

        }
    }

</script>