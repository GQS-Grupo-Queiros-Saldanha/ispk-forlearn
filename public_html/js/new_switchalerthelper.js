
(()=>{
    const btnlogautSystem = document.querySelector("#btn-logautt-sys");

    btnlogautSystem.addEventListener("click",(e)=>{
        e.preventDefault();
        swalRunnable("Tem certeza que deseja terminar a sessão?", () => {
            let link = btnlogautSystem.querySelector('a').href;
            window.open(link,"_self");
        });
    });
    
})();

function swalRunnable(text, runnable = null){
    
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
              confirmButton: 'btn btn-success ml-3',
              cancelButton: 'btn btn-danger mr-3'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            text: text,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancelar!',
            confirmButtonText: 'Sim, confirmo!',
            reverseButtons: true,
          }).then((result) => {
            if (result.isConfirmed && runnable) {
                runnable();
            }
        })
}

function general(icon, title, ...params){ 
    const tam = params.length;
    if(tam == 1)
      Swal.fire({icon: icon,title: title,html: params[0]})
    else if(tam == 2)
      Swal.fire({icon: 'warning',title: params[0],html: params[1]})
}

function warning(...params){
    switch(params.length){
        case 1:
            general('warning','Aviso',params[0]);
        break;
        case 2:
            general('warning',params[0],params[1]);
        break;
    }
}

function success(...params){
  switch(params.length){
    case 1:
        general('success','Successo',params[0]);
    break;
    case 2:
        general('success',params[0],params[1]);
    break;
  }
}

function danger(...params){
    switch(params.length){
        case 1:
            general('error','Erro',params[0]);
        case 2:
            general('error',params[0],params[1]);
        break; 
    }          
}

function information(...params){
    switch(params.length){
        case 1:
            general('info','Informa巽達o',params[0]);
        break;    
        case 2:
            general('info',params[0],params[1]);
        break; 
    }           
}

function question(...params){
    switch(params.length){
        case 1:
            general('question','Quest達o',params[0]);
        break;    
        case 2:
            general('question',params[0],params[1]);
        break; 
    }           
};