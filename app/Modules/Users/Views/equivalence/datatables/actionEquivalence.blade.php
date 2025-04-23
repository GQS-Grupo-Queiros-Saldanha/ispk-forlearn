
@if($item->state!="total")

<a href="#" class="btn btn-{{$item->status_disc == 0 ?"warning":"success"}} btn-sm alertar" name-data="{{$item->student}}" title="Adicionar disciplinas">
    @icon('fas fa-plus')
</a>
<a href="#" data-id="{{$item->id}}" data-student="{{$item->student}}" class="btn btn-danger btn-sm editChangeDelete" title="Eliminar pedido de transferência" data-toggle="modal" data-type="anular_matricula" data-target="#anulate_matricula">
    @icon('fas fa-trash') 
</a>


@elseif($item->state=="total" && ($item->type_transference==1 || $item->type_transference==3))
    <a href="{{Route('EquivalenceController.edit',$item->id)}}" class="btn btn-{{$item->status_disc == 0?"success":"dark"}} btn-sm " title="Adicionar disciplinas" target="_blank">
        @icon('fas fa-plus') 
    </a>
@else
<a href="#" class="btn btn-info btn-sm " title="Recibo de pedido de transferência {{$item->type_transference}}">
    @icon('fas fa-file-pdf') 
</a>


<a href="#" class="btn btn-danger btn-sm editChange">
    @icon('fas fa-trash')
</a>

@endif




<!-- Modal view -->


  <div class="modal fade" id="alertarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel">Atenção</h3> 
        </div>


        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                    <div class="conteudoGeral">
                    
              
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group col alert-warning p-4">
                                    <h6>
                                        Para efectuar a equivalência do(a) estudante  <label style="font-size: 14pt;font-weight: bold;" id="nameStudent"></label> é necessário que se faça o pagamento do emolumento Pedido de Transferência na tesouraria.    
                                    
                                    </h6>   
                                

                                </div>
                            </div>
                        </div>

                      
          

                    </div>
                </div>
            </div>
        </div>
            
        
        
        
        <div class="modal-footer"> 
        
          <button type="button" class="btn btn-primary close_modal" >Fechar</button>
        </div>
      </div>
    </div>
  </div>



  <script>
   
  

    $(".alertar").click(function () { 
        $("#anoLective").val($("#lective_years").val());
        $("#nameStudent").text(this.getAttribute("name-data"));
        $("#alertarModal").modal('show');
    });
    
    
    $(".close_modal").click(function () { 
        $("#alertarModal").modal('hide');
    });
    
    
    $('.editChangeDelete').on('click', function() {
        // Remover o elemento pai (li) do botão clicado
        $("#alertarModalDelete").modal('show');
         var id= $(this).attr('data-id');
         var nome= $(this).attr('data-student');
         $("#nome").text(nome);
         $("#id_transf").val(id);
         $("#nome_completo").val(nome);
      
         
    });
    

</script>