@section('title',__('Estúdio de fotografia'))
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
                        <h1>Estúdio de fotografia</h1>
                        
                    </div>
                    <div class="col-sm-6">
                      
                          
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
                                            <label>@lang('Utilizador')</label>
                                            <select name="user_id" id="utilizadorSelect_id" data-live-search="true" class="selectpicker form-control form-control-sm" required="required" id="student" data-actions-box="false" data-selected-text-format="values" name="student" tabindex="-98">
                                                <option value="">Seleciona o utilizador</option>
                                                @foreach ($student as $key=> $item)
                                                @php
                                                    $value=$item->id."@".$item->image;
                                                @endphp
                                                
                                                <option value="{{$value}}" data-pic="{{$item->fotografia}}"> {{$item->name}} ({{$item->email}})</option>
                                                
                                                @endforeach
                                            </select>
                                            <br>
                                            <video id="video">Video stream not available.</video>
                                            <br>

                                            <button type="button" class="btn btn-primary" id="startbutton" > 
                                                <i class="fas fa-camera"></i>
                                                Fotografar
                                            </button>
                                            <br>
                                            <button type="button" class="btn btn-warning" id="LimparIMG" > 
                                                <i class="fas fa-spinner"></i>
                                                Limpar 
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-6">

                                    <div class="form-group col" style="align-items: right;">
                                      <img style="background-color: grey; width:400px; height:400px;" id="photo" title="Imagem do utilizador selecionado ">

                                        
                                    </div> 
                                   
                                </div>
                             

                                <div class="col-6" id="disciplines-container" >
                                    <div class="contentarea">
                                       
                                      
                                      <div class="camera">
                                        <video id="video">Erro ao detectar a câmera! Tente novamente.</video>
                                       
                                      </div>
                                          <canvas id="canvas" hidden>
                                          </canvas>
                                   
                                       
                                    </div>  
                                    
                                </div>
                            </div>
                            
                        </div>
                        
                        <input type="hidden" name="ImageUpload" value="" id="ImageUpload" title="Tirar uma foto para o utilizador">
                        
                    
                   

                    </div>
                        <div class="col-12 justify-content-md-end">
                           
                            <div class="form-group col-4  justify-content-md-end" style="float:right;">
                                <button type="submit" id="btn-listar" class="btn btn-success  float-end"  target="_blank" style="width:180px;">
                                    <i class="fas fa-photo"></i> 
                                   Gravar
                                </button>    
                            </div>
                    </div>
                  {!! Form::close() !!}
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
//Change do usuário get photo
selectUser.change(function(){
  $("#ImageUpload").val("");
    var   path= @json($path);
    let   data = selectUser.val();
    const item=data.split('@');
    var   foto =item[1];
    var   id_user =item[0];
    var   PhotoParameter = $(this).find(':selected').attr('data-pic');
    
    if(foto!="" || PhotoParameter!=""){

        var img= foto!=""?foto:PhotoParameter;
        $("#photo").attr('src',path+img);
        $("#startbutton").show();
      }
     else{  
        // alert("Utilizador selecionado não tem nenhuma foto");
        console.log("Sem foto");
        $("#photo").attr('src',"https://triunfo.pe.gov.br/pm_tr430/wp-content/uploads/2018/03/sem-foto.jpg");
    
    }


  
});


});





// -------------------------------------------------------- //

(function() {
  // The width and height of the captured photo. We will set the
  var width = 320;    // We will scale the photo width to this
  var height = 0;     // This will be computed based on the input stream

  var streaming = false;
  // The various HTML elements we need to configure or control. These
  var video = null;
  var canvas = null;
  var photo = null;
  var ImageUpload = null;
  var startbutton = null;

  function startup() {
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');
    photo = document.getElementById('photo');
    ImageUpload = document.getElementById('ImageUpload');
    startbutton = document.getElementById('startbutton');

    navigator.mediaDevices.getUserMedia({video: true, audio: false})
    .then(function(stream) {
      video.srcObject = stream;
      video.play();
    })
    .catch(function(err) {
      console.log("An error occurred: " + err);
    });

    video.addEventListener('canplay', function(ev){
      if (!streaming) {
        height = video.videoHeight / (video.videoWidth/width);
      
        // Firefox currently has a bug where the height can't be read from
        // the video, so we will make assumptions if this happens.
      
        if (isNaN(height)) {
          height = width / (4/3);
        }
      
        video.setAttribute('width', width);
        video.setAttribute('height', height);
        canvas.setAttribute('width', width);
        canvas.setAttribute('height', height);
        streaming = true;
      }
    }, false);

    startbutton.addEventListener('click', function(ev){
      takepicture();
      ev.preventDefault();
    }, false);
    
    clearphoto();
  }

  // Fill the photo with an indication that none has been
  // captured.

  function clearphoto() {
    var context = canvas.getContext('2d');
    context.fillStyle = "#AAA";
    context.fillRect(0, 0, canvas.width, canvas.height);

    var data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
  }
  
  // Capture a photo by fetching the current contents of the video
 
  function takepicture() {
    var context = canvas.getContext('2d');
    if (width && height) {
      canvas.width = width;
      canvas.height = height;
      context.drawImage(video, 0, 0, width, height);
    
      var data = canvas.toDataURL('image/png');
      photo.setAttribute('src', data);
      ImageUpload.setAttribute('value',data);
    } else {
      clearphoto();
    }
  }

  // Set up our event listener to run the startup process
  window.addEventListener('load', startup, false);
})();

    </script>
@endsection
