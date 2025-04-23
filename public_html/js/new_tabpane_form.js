(()=>{
    
    
      console.log("Erro")
    let camposObrigatorios = document.querySelectorAll(".tab-content [required]");
    let btnEditSumit = document.querySelector('form #editUser');

    function navLinkActive(nav){
        const navLinks = document.querySelectorAll('#myTab .nav-link');
        navLinks.forEach(item => {
            if(item.id == nav.id){
                if(!item.classList.contains('active')){
                    item.classList.add('active');
                }
            }else{
                item.classList.remove('active')
            }
        })
    }

    function tabPaneActive(tab){
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabPanes.forEach(item => {
            if(item.id == tab.id){
                if(!item.classList.contains('active','show')){
                    item.classList.add('active','show');
                }
            }else{
                item.classList.remove('show','active')
            }
        })
    }        

    camposObrigatorios.forEach(item=>{
        item.addEventListener('change',(e)=>{
            if(item.value && item.value.length > 0){
                if(item.classList.contains('is-invalid')){
                    item.classList.remove('is-invalid');
                }
            }
        });
    })
    
    btnEditSumit.addEventListener('click', function(e){
        
        let elementsEmpty = [];
        let joins = [];
        elementsWithRequired = document.querySelectorAll('[required]');
        elementsWithRequired.forEach(item => {
            if(item.value == ""){ 
              
                elementsEmpty.push(item);
                
            }
        })
        
        elementsEmpty.forEach(item => {
            let label = item.parentElement.querySelector("label");
            if(label){
                joins.push(label.innerHTML.trim());
            }
        })
        
        if(joins.length > 0){
            
            warning('Preencha os campos em falta: '+joins.join(', '));
        }
        
    })

    document.addEventListener('invalid', function(event) {
        event.preventDefault(); 
        const campoInvalido = event.target;
        const tabPane = document.querySelectorAll('.tab-pane');
        tabPane.forEach(item => {
            let tab = item.querySelector(`[name="${campoInvalido.name}"]`);
            if(tab){
                let navLink = document.querySelector(`#myTab #${item.getAttribute("aria-labelledby")}`);
                if(navLink){
                    navLinkActive(navLink)
                    tabPaneActive(item);
                    if(!campoInvalido.classList.contains("is-invalid")){
                        campoInvalido.classList.add("is-invalid");
                    }
                }
            }
        });
    });

})();;