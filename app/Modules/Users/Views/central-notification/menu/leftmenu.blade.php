<div class="row">
    <div class="col-md-3">
        @if (!auth()->user()->hasAnyPermission(['estudante_envia_mensagem']))  
      <a href="{{route('central-notification.update',"?central-control=message")}}" class="btn btn-primary btn-block mb-3">Enviar mensagem</a>
       @endif
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"></h3>

          <div class="card-tools">
         
         </div>
        </div>
        <div class="card-body p-0  ">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item active" id="li_entrada">
              <a href="{{route('central-notification.update',"?central-control=inbox")}}" class="nav-link">
                <i class="fas fa-inbox"></i> Caixa de entrada
                @if (count(count_notification())>0)
                <span class="badge bg-danger float-right text-white" >{{count(count_notification())}}</span>
                @endif
              </a>
            </li>

            <li class="nav-item"  id="li_enviada">
              <a href="{{route('central-notification.update',"?central-control=sent")}}" class="nav-link">
                <i class="far fa-envelope"></i> Enviada
              </a>
            </li>

            <li class="nav-item" id="li_favourite">
              <a href="{{route('central-notification.update',"?central-control=favourite")}}" class="nav-link">
                <i class="far fa-star "></i> Marcadas
                
            </a>
          </li>


          @if (auth()->user()->hasAnyRole(['apoio-estudante']))
          <li class="nav-item">
              <a href="{{route('central-notification.update',"?central-control=star")}}" class="nav-link">
                <i class="fas fa-graduation-cap"></i>Estudante  > Instituição
               
              </a>
          </li>
          @endif
         
            <li class="nav-item"  id="li_lixeira">
                <a href="{{route('central-notification.update',"?central-control=trash")}}" class="nav-link">
                <i class="far fa-trash-alt"></i> Lixeira
              
            </a>
            </li>
           
          </ul>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
      <div class="card">
        <div class="card-header">
   
          <div class="card-tools">
        
          </div>
        </div>
        <div class="card-body p-0">
         
          
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
