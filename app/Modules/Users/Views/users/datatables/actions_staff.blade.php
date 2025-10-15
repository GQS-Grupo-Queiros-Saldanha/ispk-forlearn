<a href="{{ route('users.show', $item->id) }}" class="btn btn-info btn-sm">
    @icon('far fa-eye')
</a>
@if (!auth()->user()->hasAnyPermission(['secretario_view_RH']))
  <a href="{{ route('users.edit', $item->id) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
  </a>  
@endif


@if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger btn-user-del' 
     url="{{ route('users.destroy',$item->id) }}">
        @icon('fas fa-trash-alt')
    </button>
@endif

@if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn','Chefe da Secção de Processamento Salarial']))
<a href="{{ route('users.roles', $item->id) }}" class="btn btn-light btn-sm">
    <i class="fas fa-user-shield"></i>
    @lang('Users::roles.roles')
</a>

{{-- <a href="{{ route('users.permissions', $item->id) }}" class="btn btn-light btn-sm">
    <i class="fas fa-scroll"></i>
    @lang('Users::permissions.permissions')
</a> --}}
@endif

<script class="script-del">
    var btnUserDels = document.querySelectorAll(".btn-user-del");
    
    scriptOne();
    
    function scriptOne(){
        let scripts = document.querySelectorAll(".script-del");
        let tam = scripts.length;
        if(tam > 1){
            for(let i = 0; i < tam - 1; i++)
                 scripts[i].remove();
        }
    }
    
     btnUserDels.forEach( item => {
         item.addEventListener('click',(e)=>{
             let modal = $('#modal_confirm');
             let url = item.getAttribute('url');
             let form = document.querySelector("#form_modal_confirm");
             let method = form.querySelector("[name='_method']");
             modal.modal('show')
             form.action = url;
             method.value = "DELETE";
         });
     });
</script>