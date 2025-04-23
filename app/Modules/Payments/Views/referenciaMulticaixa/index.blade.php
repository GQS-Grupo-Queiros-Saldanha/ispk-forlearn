@section('title',__('REFERÊNCIA MULTICAIS'))


@extends('layouts.backoffice')

@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
 <div class="content-panel pb-3" style="padding: 0px;">
    @include('Payments::requests.navbar.navbar')
    <style>
        .fotoUserFunc {
                width: 70%;
            margin: 0px;
            padding: 0px;
            shape-outside: circle();
            clip-path: circle();
            

            border-radius: 50%;
            background-color: #c4c4c4;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 40%;
            width: 150px;
            height: 150px;
            -webkit-filter: brightness(.9);
            filter: brightness(.9);
            border: 5px solid #c5fff9;
                /* -webkit-transition: all .5s ease-in-out; */
                /* transition: all .5s ease-in-out; */
         }
    </style>
    <div style="z-index: 1900" class="modal fade" id="load-notification" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered"> 
          <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
      </div>
  </div>
    <div class="content-header">
        <div class="container-fluid " style="padding-left: 5rem!important; padding-right: 5rem!important;">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"> 
                    </h1>
                </div>
                <div class="col-sm-6">
                    {{ Breadcrumbs::render('requests.transaction') }}
                </div>
            </div>
        </div>
    </div>
        <div class="container-fluid mb-5" style="padding-left: 5rem!important; padding-right: 5rem!important;">
            <div class="jumbotron mb-5  p-2"  style="background-color: #c5fff6;border-radius: 10px;">
                <h1 class="m-0 text-dark">Referência Multicaixa</h1>
                <div class="pr-3 pl-3 pb-3 pt-3 ">
                    <div class="row mb-5">
                        <div class="col-sm-9">
                         
                            <div class="row">
                                <div class="col-sm-5">
                                  <div  style="background: #343a40;color:white" class="card rounded">
                                     <small class="col-12 pt-2 pb-2 border-bottom">Informações sobre referência</small>
                                    <div class="card-body">
                                        <h6 class="card-title"> Entidade: <small>{{$getTableReferencia[0]->entidade}}</small></h6>
                                         @php $referencia= rtrim(chunk_split($getTableReferencia[0]->referencia, 3, '-'), '-'); @endphp
                                        <h5 class="card-title mt-2"> Referência: <small>{{$referencia}}</small></h5>
                                        <h5 style="font-size: 0.9pc" class="card-title mt-2 mb-2">Expira a: <small style="font-size: 0.9pc" class="card-text">{{$getTableReferencia[0]->data_expira}}</small></h5>  
                                        {{-- <small style="color:#1e9bf5;" class="card-text">Referência multicaixa pronta a ser usada</small> --}}
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-5">
                                  <div style="background: #1e9bf5;color:white" class="card rounded">
                                    <small class="col-12 border-bottom pt-2 pb-2">Informações sobre o pagamento</small>
                                    <div class="card-body ">
                                      <h5 class="card-title">Montante: <small class="card-title">{{number_format($getTableReferencia[0]->montante, 2, ',', '.') }}</small> <small class="card-text">Kz</small></h5>
                                      <small class="card-text">Valor total a pagar.</small>

                                      <h5 class="card-title card-title mt-2">Saldo em carteira</h5>
                                      <h5 class="card-text">{{number_format($getTableReferencia[0]->credit_balance, 2, ',', '.') }}</h3>
                                      {{--<a-- href="#" class="btn btn-primary">Go  somewhere</a--}}
                                    </div>
                                  </div>
                                </div>
                              </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="col-auto foto-estudante">
                                <center class="p-0">
                                    <div class="fotoUserFunc" style="background-image: url('//{{$_SERVER['HTTP_HOST']}}/users/avatar/{{$getTableReferencia[0]->foto}}')"></div>
                                    <h4 class="mt-1 pb-0 mb-0 profile-username text-center name-user">{{$getTableReferencia[0]->nome}}</h4>
                                    <a class="text-muted p-0 m-0 text-center">Matricula: <strong class="user-contrato">{{$getTableReferencia[0]->matricula}}</strong></a>
                                </center> 
                                
                            </div>
                        </div>
                    </div>
                    <p class="lead">
                      <div hidden  class="alert text-white rounded" style="background: #42c931" role="alert">
                      <h5 class="p-0  m-0">Notificação enviada com sucesso.</h5>
                      </div>
                    </p>
                    <hr class="my-3">
                    <p>A referência multicaixa é utilizada para efeito de liquidação dos emolumentos/propinas listada na tabela <i class="fa fa-down-long"></i>.</p>
                    <div class="mt-2 mb-5">
                        <table class="table table-striped table-Warning">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Emolumentos/Propinas</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Pagamento</th>
                              </tr>
                            </thead>
                            <tbody style="background: #bbfff5" class="table-warning">
                              @php
                                  $i=0;
                              @endphp
                              @foreach ($getTableReferencia as $item)
                              @php $i++ @endphp
                                <tr>
                                  <th scope="row">{{$i}}</th>
                                  <td>
                                    {{$item->nomeEmolumento}}
                                      @if($item->month == 1)
                                      ( Janeiro {{ $item->year}} )
                                      @elseif($item->month == 2)
                                          ( Fevereiro {{ $item->year}} )
                                      @elseif ( $item->month == 3)
                                          ( Março {{ $item->year}} )
                                      @elseif ($item->month == 4)
                                          ( Abril {{ $item->year}} )
                                      @elseif ($item->month == 5)
                                          ( Maio {{ $item->year}} )
                                      @elseif ($item->month == 6)
                                          ( Junho {{ $item->year}} )
                                      @elseif ($item->month == 7)
                                          ( Julho {{ $item->year}} )
                                      @elseif ($item->month == 8)
                                          ( Agosto {{ $item->year}} )
                                      @elseif ($item->month == 9)
                                          ( Setembro {{ $item->year}} )
                                      @elseif ($item->month == 10)
                                          ( Outubro {{ $item->year}} )
                                      @elseif ($item->month == 11)
                                          ( Novembro {{ $item->year}} )
                                      @elseif ($item->month == 12)
                                          ( Dezembro {{ $item->year}} )
                                      @endif 
                                    @if ($item->nome_disciplina!=null)
                                      ({{$item->nome_disciplina}} - [{{$item->codigo_disciplina}}])
                                    @endif
                                  </td>
                                  <td> {{number_format($item->base_value, 2, ',', '.') }} <small>Kz</small></td>
                                  <td>
                                    @if ($item->status == 'pending')
                                        <span class="bg-info p-1">ESPERA</span>
                                    @elseif($item->status == 'total' )
                                        <span class="bg-success p-1 text-white">PAGO</span>  
                                    @elseif($item->status == 'partial' )
                                        <span class="bg-warning p-1">PARCIAL</span>
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                    </div>
                    <p class="lead mt-3">
                        <button id="btn-notificacao-referencia" style="background: #343a40; color: white" class="btn  btn-lg rounded"  role="button">Enviar notificação</button>
                    </p>
                </div> 
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
@parent
<script>
    var  id_referencia='{{$getTableReferencia[0]->id_referencia}}';
  console.log(id_referencia)
    $("#btn-notificacao-referencia").click(function (e) { 
      $("#load-notification").modal('show')
      
      $.ajax({
        url: "tesouraria-notication_referencia/" +id_referencia,
        type: "GET",
        data: {
          _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
        success: function (response) {
          
        }
      }).done(function (data) {
        $(".alert").attr('hidden',false)
        
        $("#load-notification").modal('hide')
        $("#load-notification").modal('hide')
        setTimeout(() => {
          $(".alert").slideUp(1500,close());
          function close(){
            console.log(data)
            $(".alert").attr('hidden',true)
          }
          $("#load-notification").modal('hide')
        }, 2800);
      })
    });
</script>
@endsection