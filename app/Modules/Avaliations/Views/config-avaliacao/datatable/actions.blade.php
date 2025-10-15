<button class='btn btn-sm btn-warning btn-edit-config' data-toggle="modal" data-target="#exampleModalCenter" type="submit" 
    data-action="{{ route('avaliacao.config.update', $item->id) }}" data-method="PUT">
    @icon('fas fa-edit')
</button>

<button class='btn btn-sm btn-danger btn-delete-config'  type="submit" data-toggle="modal" data-target="#exampleModalCenter"  
     data-action="{{ route('avaliacao.config.destroy', $item->id) }}" data-method="DELETE">
    @icon('fas fa-trash-alt')
</button>

<script class="script-page">
    const btnEditConfigs = document.querySelectorAll('.btn-edit-config'); 
    const btnDeleteConfigs = document.querySelectorAll('.btn-delete-config');
    
    eliminarbtn();

    function eliminarbtn(){
        let scrips = $('.script-page');
        for(let i = 0; i <= scrips.length - 1; i++)
            $(scrips[i]).remove();
    }
    
    function createLectiveYear(){
        const select = document.querySelector("#lective_year");
        const option = select.querySelector(`[value="${select.value}"]`)
        document.querySelector("#lective_year_desc").value = option.innerHTML.trim();
        document.querySelector("#lective_year_param").value = option.value.trim();
    }
    
    function initValueInform(exame_nota,exame_nota_inicial,exame_nota_final,mac_nota_recurso,mac_nota_dispensa,percentagem_mac,percentagem_oral){
        document.querySelector("#exame_nota").value = exame_nota;
        document.querySelector("#exame_nota_inicial").value = exame_nota_inicial;
        document.querySelector("#exame_nota_final").value = exame_nota_final;
        document.querySelector("#mac_nota_recurso").value = mac_nota_recurso;
        document.querySelector("#mac_nota_dispensa").value = mac_nota_dispensa;
        document.querySelector("#percentagem_mac").value = percentagem_mac;
        document.querySelector("#percentagem_oral").value = percentagem_oral;
    }
    
    function selectorStrategy(value){
        let html = "";
        let options = [];
        const selector = document.querySelector("#strategy");
        selector.querySelectorAll('option').forEach( item => options.push({ value: item.value.trim(), text: item.innerHTML.trim() }) );
        options.forEach(item => html += `<option value="${item.value}" ${item.value == value ? 'selected' : ''}>${item.text}</option>` )
        selector.innerHTML = html;
    }
    
    function formAction(action, method = "POST"){
        const form = document.querySelector("#form-avaliacao-config")
        form.action = action;
        form.querySelector("[name='_method']").value = method;
    }
    
    function activeOrDesactive(status){
        const form = document.querySelectorAll("#form-avaliacao-config .form-control");
        if(status){
            form.forEach(item => {
                if(item.hasAttribute('disabled')) item.removeAttribute('disabled');
            })
        }else{
            form.forEach(item => {
                if(!item.hasAttribute('disabled')) item.setAttribute('disabled',true);
            })
        }
    }
    
    function actionUpdateOrDelete(item,action){
        formAction(item.dataset.action, item.dataset.method);
        
        activeOrDesactive(action);
            
        document.querySelector("#exampleModalLongTitle").innerHTML = action ? "Editar configuração" : "Eliminar configuração";
        document.querySelector("#btn-save").innerHTML = action 
            ? "<i class='fas fa-edit'></i><span>Guardar</span>" 
            : "<i class='fas fa-check'></i><span>Confirmo</span>";
            
        const row = item.parentElement.parentElement;
        const lines = row.querySelectorAll("td");
            
        createLectiveYear(); 
        selectorStrategy(lines[2].innerHTML.trim());
        
        initValueInform(
            lines[3].innerHTML.trim(),
            lines[4].innerHTML.trim(),
            lines[5].innerHTML.trim(),
            lines[6].innerHTML.trim(),
            lines[7].innerHTML.trim(),
            lines[8].innerHTML.trim(),
            lines[9].innerHTML.trim(),
        );
    }
    
    btnEditConfigs.forEach( item => {
       item.addEventListener('click',(e) => actionUpdateOrDelete(item, true)) 
    });    
    
    btnDeleteConfigs.forEach( item => {
       item.addEventListener('click',(e) => actionUpdateOrDelete(item, false)) 
    });
    
</script>