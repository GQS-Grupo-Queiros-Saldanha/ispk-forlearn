@section('title',__('Estúdio de fotografia-grade'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<div class="content-panel" style="padding: 0px;">
    @include('Users::studio-photo.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Estúdio de fotografia - Album</h1>
                        
                    </div>
                    <div class="col-sm-6">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('studio-photo.index') }}">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Album de fotos</li>
                            </ol>
                        </div>
                         
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        
                    </div>
                </div>

               
            
               
   
                <div class="row">

                    <div class="col">
                        {!! Form::open(['route' => ['save_photografy_user'],'method'=>'post','required'=>'required','target'=>'_blank']) !!}
                            @csrf
                            @method('post')
                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <div class="form-group col">
                                            <label>@lang('Seleciona o utilizador')</label>
                                            <select name="user_id" id="utilizadorSelect_id" data-live-search="true" class="selectpicker form-control form-control-sm" required="required" id="student" data-actions-box="false" data-selected-text-format="values" name="student" tabindex="-98">
                                                <option value="">Seleciona o utilizador</option>
                                                @foreach ($student as $key=> $item)
                                             
                                                <option value="{{$item->id}}"> {{$item->name}} ({{$item->email}})</option>
                                                
                                                @endforeach
                                            </select>
                                            <br>
                                           
                                            <br>

                                         
                                        </div>
                                    </div>
                                </div>


                                <div class="col-6">


    
                               </div> 
                               <div class="row" style="">

                                <style>
                                  .gallery{
                                            padding: 80px 0px;
                                        }
                                        img{
                                            max-width: 100%;
                                        }
                                        .gallery img{
                                            background: #999;
                                            padding: 15px;
                                            width: 100%;
                                            box-shadow: 0px 0px 15px rgba(0,0,0,0.3);
                                            cursor: pointer;
                                        }
                                        #gallery-popup .modal-img{
                                            width: 100%;
                                        }

                                </style>

                                <section class="gallery min-vh-100">

                                    <div class="container-lg p-5">
                                        <center><h2 id="quantidadeFotos"></h2></center>
                                        <div class="row gy-4 row-cols-1 row-cols-sm-2 row-cols-md-3 Grade-image" id="">

                                        </div>
                                    </div>

                                </section>

                                    </div>
                            </div>
                        </div>
                    </div>







                                        
                                    </div> 
                                   
                                </div>
                             

                               
                            </div>
                            
                        </div>
                        
                        <input type="hidden" name="ImageUpload" value="" id="ImageUpload" title="Tirar uma foto para o utilizador">
                        
                    
                   

                    </div>
                        <div class="col-12 justify-content-md-end">
                           
                            <div class="form-group col-4  justify-content-md-end" style="float:right;">
                                {{-- <button type="submit" id="btn-listar" class="btn btn-success  float-end"  target="_blank" style="width:180px;">
                                    <i class="fas fa-photo"></i> 
                                    Gravar
                                </button>     --}}
                            </div>
                    </div>
                  {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


    <div class="col-6" id="disciplines-container" >
        <!-- Modal -->
<div class="modal fade" id="gallery-popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="border-radius: 8px;">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">
<div class="modal-header">
<h3 ></h3>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: #999; color:white; border-radius:4px;" id="fecharModal">X</button>
</div>
<div class="modal-body">
<img src="" class="modal-img" alt="Modal Image" id="imagemFull">
</div>
<div class="modal-footer">

<button class="btn btn-danger" id="DeletePhoto" data-id="">
<i class="fas fa-trash"></i>
Eliminar
</button>

</div>
</div>
</div>
</div>

</div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection
@section('scripts')
@parent


<script>    

let selectUser = $('#utilizadorSelect_id');


$(document).ready(function () {
    // $("#startbutton").hide();

    $("#LimparIMG").click(function(){
        $("#photo").attr('src',"https://triunfo.pe.gov.br/pm_tr430/wp-content/uploads/2018/03/sem-foto.jpg")
    });

    //Modal e pegar cada item da lista de foto
    $(document).on('click', '.gallery-item', function(){
        var  src = $(this).attr('src');
        var  id = $(this).attr('id');
        $("#imagemFull").attr('src',src);
        $("#DeletePhoto").attr('data-id',id);
        $("#gallery-popup").modal('show'); 
    });


     //Eliminar photo
    $("#DeletePhoto").click(function () {
        var id_foto= $("#DeletePhoto").attr('data-id');
        var routa='/pt/users/deletePicture/'+id_foto
        $(location).attr('href',routa)
    });



     //Fechar modal
    $("#fecharModal").click(function () {
        $("#gallery-popup").modal('hide');
    });

//Change do usuário get photo
selectUser.change(function(){

  $("#ImageUpload").val("");
    var path= @json($path);

    if(selectUser.val()!=""){
        var id_user=selectUser.val();
        $(".Grade-image").empty();
         GetPicture(id_user,path);
     }
     else{  
        console.log("Sem utilizador selecionado !");
    }

    });


});


function GetPicture(id_user,path){
    let body ="";
    let foto ="";

    $.ajax({
                type: "get",
                url:'/pt/users/getPicture/'+id_user,
                data: "data",
                success: function (response) {
               

                if(response.length){
           
                    var qtd=response.length +" encontrada(s)"
                    $("#quantidadeFotos").text(qtd);
                    // console.log(response);
                    $.each(response, function (indexInArray,value) { 

                        foto=path+value.image;
                        body+='<div class="col"><img src="'+foto+'"    alt="'+value.name+'"  class="gallery-item" id="'+value.id+'"></div>';
                        foto="";
                    });
                    
                     $(".Grade-image").append(body);

                    }
                  
                }
            });

}



    </script>
@endsection
