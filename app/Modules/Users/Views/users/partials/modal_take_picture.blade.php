<style>
       
    svg:not(:root) {
      display: block;
    }

    .playable-code {
      background-color: #f4f7f8;
      border: none;
      border-left: 6px solid #558abb;
      border-width: medium medium medium 6px;
      color: #4d4e53;
      height: 100px;
      width: 90%;
      padding: 10px 10px 0;
    }

    .playable-canvas {
      border: 1px solid #4d4e53;
      border-radius: 2px;
    }

    .playable-buttons {
      text-align: right;
      width: 90%;
      padding: 5px 10px 5px 26px;
    }

    #video {
border: 1px solid #444;
box-shadow: 2px 2px 3px gray;
border-radius: 4px;
width:320px;
height:250px;
}

#photo {
border: 1px solid #444;
box-shadow: 2px 2px 3px gray;
border-radius: 4px;
width:320px;
height:250px;
}

#canvas {
display:none;
}

.camera {
width: 320px;
display:inline-block;
}

.output {
width: 320px;
display:inline-block;
vertical-align: top;
}

#startbutton {
display:block;
position:relative;
margin-left:auto;
margin-right:auto;
padding: 10px 10px;
bottom:32px;
background-color: rgba(24, 24, 24, 0.5);
border: 1px solid rgba(255, 255, 255, 0.7);
box-shadow: 0px 0px 1px 2px rgba(0, 0, 0, 0.2);
font-size: 14px;
font-family: "Lucida Grande", "Arial", sans-serif;
color: rgba(255, 255, 255, 1.0);
}

.contentarea {
font-size: 16px;
font-family: "Lucida Grande", "Arial", sans-serif;
width: 760px;
}

</style>
  
  
  <!-- Modal -->
  <div class="modal fade" id="TakePictureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel"></h3> 
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                        <center>
                            <h4 class="title">FOTOGRAFIA</h4>
                            <div class="contentarea">
                                <div class="camera">
                                 <video id="video" width="320" height="240">Câmera não habilitada.</video>
                                 <button id="startbutton">Tirar foto</button>
                               </div>
            
                               <canvas id="canvas" width="320" height="240"> </canvas>
                            
                               <div class="output">
                                 <img id="photo"  alt="A captura aparecerá aqui!." src="">
                               
                               </div>
                            
                        </div>
                    </center>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          
          <button type="button" class="btn btn-success" id="guardar_foto">Guardar</button>
          <button type="button" class="btn btn-primary" id="close_modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>








<script>


  
  const width = 320; // We will scale the photo width to this
  let height = 0; // This will be computed based on the input stream

 

  let streaming = false;

  // The various HTML elements we need to configure or control. These
  // will be set by the startup() function.

  let video = null;
  let canvas = null;
  let photo = null;
  let startbutton = null;

  function showViewLiveResultButton() {
    if (window.self !== window.top) {
   
      document.querySelector(".contentarea").remove();
      const button = document.createElement("button");
      button.textContent = "View live result of the example code above";
      document.body.append(button);
      button.addEventListener('click', () => window.open(location.href));
      return true;
    }
    return false;
  }

  function startup() {
    if (showViewLiveResultButton()) { return; }
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');
    photo = document.getElementById('photo');
    fototiradaInput = document.getElementById('fototirada');
    startbutton = document.getElementById('startbutton');

    navigator.mediaDevices.getUserMedia({video: true, audio: false})
      .then((stream) => {
        video.srcObject = stream;
        video.play();
      })
      .catch((err) => {
        console.error(`An error occurred: ${err}`);
      });

    video.addEventListener('canplay', (ev) => {
      if (!streaming) {
        height = video.videoHeight / (video.videoWidth/width);

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

    startbutton.addEventListener('click', (ev) => {
      takepicture();
      ev.preventDefault();
    }, false);

    clearphoto();
  }
// Preencha a foto com a indicação de que nenhuma foi
// capturado.

  function clearphoto() {
    const context = canvas.getContext('2d');
    context.fillStyle = "#AAA";
    context.fillRect(0, 0, canvas.width, canvas.height);

    const data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
   
  }
   // Capturar uma foto buscando o conteúdo atual do vídeo
   // e desenhando-o em uma tela, convertendo-o em um PNG
   // formata URL de dados. Ao desenhá-lo em uma tela fora da tela e, em seguida,
   // desenhando isso na tela, podemos alterar seu tamanho e/ou aplicar
   // outras alterações antes de desenhá-lo.

  function takepicture() {
    const context = canvas.getContext('2d');
    if (width && height) {
      canvas.width = width;
      canvas.height = height;
      context.drawImage(video, 0, 0, width, height);

      const data = canvas.toDataURL('image/png');
      photo.setAttribute('src', data);
      fototiradaInput.setAttribute('value', "");
      fototiradaInput.setAttribute('value', data);
    } else {
      clearphoto();
    }
  }

// Configura nosso ouvinte de eventos para executar o processo de inicialização
// uma vez que o carregamento está completo.
  window.addEventListener('load', startup, false);




</script>