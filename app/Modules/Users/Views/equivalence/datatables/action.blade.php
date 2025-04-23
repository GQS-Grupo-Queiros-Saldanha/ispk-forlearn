
<a href="#" class="btn btn-info btn-sm viewBtn" >
        @icon('fas fa-eye')
</a>
<a href="#" class="btn btn-warning btn-sm editChange" id="{{$item->id}}">
    @icon('fas fa-edit')
</a>
{{-- <a href="#" class="btn btn-info btn-sm " id="{{$item->id}}">
    @icon('fas fa-edit')
</a> --}}
<a href="{{route('change.courses.disciplina.list',$item->id)}}" target="_blank" class="btn btn-success btn-sm " id="" title="ver disiplinas">
    @icon('fas fa-book')
</a>

@if(auth()->user()->hasAnyPermission(['Apagar-candidatura']))
 
@endif

<script>

    function optionUpdate(id_equ,select, key){
        $.ajax({
            url: "curso-disciplina",
            method: "GET",
            data: { 
                id_eq: id_equ,
                return_type: key,
            },
            success: function(response){
                let option = "";
                response.forEach(element => {
                    option += "<option value='"+element.discipline_id+"'>#"+element.code+" - "+element.display_name+"</option>"; 
                });
                select.html(option);
            }
        });
    }

    $(".editChange").click(function () { 
        var curso_P='{{$item->curso_de}}';
        var curso_D='{{$item->curso_para}}';
        
        $("#courseP").val(curso_P);
        $("#courseD").val(curso_D);
        $("#CreateCursoChangeEquivalence").modal('show');


        let select1 = $('#id_curso_1');
        let select2 = $('#id_curso_2');
        let id_equ = $(this).attr('id');
        $('#btn_add').val(id_equ);

        array1 = [];
        array2 = [];
        array_ids = [];

        $('#list_equivalencia').html(" ");
        
        optionUpdate(id_equ,select1,'course_id_primary');
        optionUpdate(id_equ,select2,'course_id_secundary');

        $("#scroll").css({"overflow-y":"auto", "max-height":"200px","margin-top":"10px", "border":"1px solid #ccc","border-radius":"0.5rem"});

    });



    $(".viewBtn").click(function () { 
    
        $("#historicStateModal").modal('show');
    });


    $("#close_modal").click(function () { 
        $("#historicStateModal").modal('hide');
    });


</script>


<!-- Modal view -->


  <div class="modal fade" id="historicStateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel"></h3> 
        </div>


        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                    <div class="conteudoGeral">
                        <h3>Descrição</h3>
                        @if ($item->estado==1)
                        <div class="bg-success p-2 text-white">Estado activo</div>
                        @else    
                        <div class="bg-danger p-2 text-white">Estado inativo</div>
                        @endif
                        <br>
                        <p>{{$item->descricao}}</p>

                    </div>
                </div>
            </div>
        </div>


        <div class="modal-footer">    
          <button type="button" class="btn btn-primary" id="close_modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>