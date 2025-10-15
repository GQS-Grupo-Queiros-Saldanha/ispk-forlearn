
<title>Notificações | forLEARN® by GQS</title>
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
/>
    <div class="content-panel" style="padding: 0px">
        @include('Users::central-notification.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <?php 
                        $vector_m=["trash"=>"Lixeira","sent"=>"Enviada(s)","inbox"=>"Caixa de entrada","star"=>"Marcada(s)","message"=>"Enviar mensagem","favourite"=>"Marcadas"];
                         
                        $vector_n=["smsSingle"=>"Mensagem","to"=>"Mensagem enviada","trash_view"=>"Mensagem  eliminada","without"=>""];
                        $central_control=isset($central_control) ? $central_control:"Not";
                        $mensag=isset($_GET['central-control'])? $_GET['central-control']:$central_control;

                        ?>
                    <div class="col-sm-6">
                        <h1><?php echo $vector_m[$mensag]?? $vector_n[$mensag]??"Caixa de entrada" ?> </h1>
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
                    {{-- <div class="col-md-6">
                        @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']))
                            <a id="criarCalendario" href="" class="btn btn-primary mb-3 ml-4">
                                @icon('fas fa-plus-square')
                                Criar novo calendário de prova
                            </a>
                        @endif
                    </div> --}}
                    <div class="col-md-6">
        
                    </div>
                </div>

                    <div class="row">
                        <div class="col">

                        <div class="card">
                            <div class="card-body">

                              
                          
    <style>
      .table td{
        cursor: pointer;
        padding:10px;
      }  
      .table td:hover{
        cursor: pointer;
        padding:10px;
      }  
    
    </style> 
                             
    <!-- Main content -->
    <section class="content">
      
      @include('Users::central-notification.menu.leftmenu')

          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                {{-- <h3 class="card-title">Lista de notificações</h3> --}}
  
                <div class="card-tools">
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="caixa_pesquisar" placeholder="procurar notificação" style="display: none;">
                    <div class="input-group-append" >
                      <div class="btn btn-primary" id="Pesquisar" style="display: none;">
                        <i class="fas fa-search"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="mailbox-controls">
         
                </div>
               
                <div class="table-responsive mailbox-messages  animate__animated animate__slideInRight" >
                  <!-- /.incluir as paginas da central -->
                   @php
                     $type="";
                   @endphp
                   @isset($_GET['central-control'])
                   @php
                        $type=$_GET['central-control'];
                        
                   @endphp
                   @endisset
                   @isset($central_control)
                     @php
                     $type=$central_control=="Not"?$type:$central_control;
                     @endphp
                   @endisset
             
                  @switch($type)
                      @case("message")
                          @include('Users::central-notification.menu.message')        
                      @break

                      @case("inbox")
                          @include('Users::central-notification.menu.listnotification')
                      @break

                      @case("trash")
                          @include('Users::central-notification.menu.trash')
                      @break

                      @case("sent")
                          @include('Users::central-notification.menu.sent')
                      @break

                     @case("sms")
                          @include('Users::central-notification.menu.leitorsms')
                     @break
                     @case("favourite")
                          @include('Users::central-notification.menu.favourite')
                     @break

                     @case("smsSingle")
                          @include('Users::central-notification.menu.singlesms')
                     @break

                     @case("to")
                          @include('Users::central-notification.menu.singlesms')
                     @break

                     @case("trash_view")
                          @include('Users::central-notification.menu.singlesms')
                     @break

                     @default

                     @include('Users::central-notification.menu.listnotification')
                          
                  @endswitch

                  

                </div>
                <!-- /.mail-box-messages -->
              </div>
              <!-- /.card-body -->
             
                  <!-- /.float-right -->
                </div>
              </div>
            </div>
            <!-- /.card -->

            
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
  
  
  
  













                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="data-info" hidden>
        <div id="name-info">{{ auth()->user()->name }}</div>
        <div id="email-info">{{ auth()->user()->email }}</div>
        @isset($institution->id)
            <div id="abrev-info">{{ $institution->abrev }}</div>
        @endisset
    </di>
    {{-- modal confirm --}}
    @include('Users::central-notification.modal.modal')

@endsection





















@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
   

    // const dados= fetch("http://forlearn.ao/api/Login/e.exemplo@forlearn.ao/ispm2019");


    var url=[];
    $(document).ready(function() {




        $("#compose-textarea").val("");

        $("#trash_id").click(function(){
            $("#modal_confirm").modal('show');
        });
        $("#delete-btn_notify").click(function(){
            $("#form_notify").submit();
        });
        //Checar todos
        $("#check_all").click(function(){
            let isChecked = $('#check_all').is(':checked');
            if(isChecked){
                $(".check1").attr('checked',true);
            }else{
                $(".check1").attr('checked',false);
            }
        });

    });
    
    
    function abrir(element){
        console.log(element);
         var id=element.getAttribute("data-id");
         var url = "{{ route('smsSingle', ['id' => ':id']) }}";
         url = url.replace(":id",id);
         //isso vai corrigir a string gerada com o id correto.
         $(location).attr('href',url)
    }


    
         
    function marcar(element){
                      var id=element.getAttribute("data-id");
                      var id_element="estrela_"+id;

                      
                       
                      

                      var pesquisa = $("#caixa_pesquisar").val();
                      var token = $(this).data("token");
                      $.ajax({
                        url: "{{route('marcar_estrela')}}",
                        data: {
                           "id":id,
                           "_token": token,
                         },
                        dataType: "json",

                        beforeSend:function(){
                            if(id==""){
                            console.log("sem nada na caixa");
                            return false;
                            }
                        },
                        success: function (e) {
                            // "color:#999;":"color:#ffed4a!important;"
                                if(e==1) {
                                    $('.'+id_element).css("color","#ffed4a")
                                }else if(e==2){
                                    $('.'+id_element).css("color","#999")
                                }
                                 else {
                                    console.log("Ouve algum erro na marcação de estrela ")
                                }
                                // "fas fa-star text-warning":
                                //"fas fa-star text-warning"
                        }
                      });
                    }




    </script>
@endsection
