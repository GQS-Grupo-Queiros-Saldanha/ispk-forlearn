@section('title',__('Config_code-dev'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')
    <style>
        .list-group li:hover{
            cursor: pointer;
            background: #eef9fd;
            font-size: 1pc;
            transition: all 0.5s;
        }
    </style>

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                        ×
                    </button>
                    <h5>@choice('common.error', $errors->count())</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="row mb-2 ">
                    <div class="col-sm-6 ">
                        <h1 class="m-0 text-dark">code developer </h1>
                    </div>
                    <div class="col-sm-6">
                      {{-- <a href="#"> Codigo em categoria</a> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- Main content --}}
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                <div  class="nav flex-column nav-pills me-3 col-md-3   mr-0  pr-0" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <ul class="list-group list-group-flush">
                        <li  data-link="criarCategoria" class="list-group-item"><button style="border: none; background: none; outline-style: none;" class="m-0 p-0" >Criar categorias</button></li>
                        <li  data-link="criarCodigo" class="list-group-item"><button style="border: none; background: none; outline-style: none;" class="m-0 p-0" >Criar codigo na categoria</button></li>
                        <li  data-link="associarCodigo" class="list-group-item"><button style="border: none; background: none; outline-style: none;" class="m-0 p-0" >Associar codigo - (categoria)</button></li>
                        {{-- <li id="link" class="list-group-item"><button style="border: none; background: none; outline-style: none;" class="m-0 p-0" ></button></li> --}}
                        {{-- <li id="link" class="list-group-item"><button style="border: none; background: none; outline-style: none;" class="m-0 p-0" >Criar categoraias</button></li> --}}
                        
                    </ul>
                </div>


                <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col-md-9" id="v-pills-tabContent">
                  <div class="criarCategoria col-auto ml-0 mr-0 pl-0 pr-0 pb-4 " >
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #a7e315; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                            <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase">Criar categorias</h5>
                        <div class="col-md-4 ">
                           <form method="post" action="{{ route('created_categoria') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Nome da categoria</label>
                                    <input required class="form-control" type="text" name="nome_categira" placeholder="Digite o nome da categoria" aria-label="default input example">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Codigo</label>
                                    <input required class="form-control" type="text" name="codigo_categoria" placeholder="Digite o codigo" aria-label="default input example">
                                </div>
                                
                                <button style="background: #a7e315; width: 10pc;color: white" type="submit" class="btn"><i class="fas fa-plus-circle"></i> Guardar</button>
                            </form> 
                        </div>
                        <div class="col-md-8 mt-3">
                            <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">codigo categoria</th>
                                    <th scope="col">Criado por</th>
                                    <th scope="col">Criado em</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    @php $i=1; @endphp
                                    @foreach ($getcategaria as $item)
                                    <tr>
                                        <th scope="row">{{$i++}}</th>
                                        <td>{{$item->nome_categoria}}</td>
                                        <td>{{$item->nome_code}}</td>
                                        <td>{{$item->nomeCriador}}</td>
                                        <td>{{$item->dataCriacao}}</td>
                                    </tr>
                                    @endforeach
                                 
                                  
                                </tbody>
                              </table>
                        </div>
                        
                    </div>
                  </div>
                  <div hidden class="criarCodigo " >
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                        <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase">Criar codigo na categoria</h5>
                        <div class="col-md-4 ">
                           <form method="post" action="{{ route('created_codigoInCategory') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Categoria</label>
                                    <select id="categoria" name="categoria"   class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                        <option selected ></option>
                                    
                                        @foreach ($getcategaria as $item)
                                            <option value="{{$item->id_categoria}}">{{$item->nome_categoria}} -  ({{$item->nome_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Nome</label>
                                    <input required ="form-control" type="text" name="nome_codigo" placeholder="Digite o nome do codigo" aria-label="default input example">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Codigo</label>
                                    <input required class="form-control" name="codigo" type="text" placeholder="Digite o codigo" aria-label="default input example">
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Nota</label>
                                    <textarea  name="nota" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                  </div>
                                
                                <button style="background: #20c7f9; width: 10pc;color: white" type="submit" class="btn"><i class="fas fa-plus-circle"></i> Guardar</button>
                            </form> 
                        </div>
                        <div class="col-md-8 mt-3">
                            <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nome do codigo</th>
                                    <th scope="col">Codigo</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">Nota</th>
                                    <th scope="col">Criado por</th>
                                    <th scope="col">Criado em</th>
                                  </tr>
                                </thead>
                                <tbody  class="listaCode">
                                   
                                </tbody>
                              </table>
                        </div>
                        
                    </div>
                  </div>
                  <div hidden class="associarCodigo" >
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #7eaf3e; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-5 "></div>
                        
                        <div class="col-md-4 ">
                           <form>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Categoria</label>
                                    <select  id="codeInCategoria" name="categoria"   class="selectpicker form-control form-control-sm" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true"   required data-selected-text-format="values"  tabindex="-98">
                                        <option selected ></option>
                                    
                                        @foreach ($getcategaria as $item)
                                            <option value="{{$item->nome_code}}">{{$item->nome_categoria}} -  ({{$item->nome_code}})</option>
                                        @endforeach
                                    </select>
                                </div>        
                                <button  id="incluirCodigo" style="background: #7eaf3e; width: 10pc;color: white" type="button" class="btn"><i class="fas fa-pen-to-square"></i> Editar </button>
                            </form> 
                        </div>
                        <div class="col-md-8 mt-3">
                            <table class="table table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">#</th>
                                    <th scope="col">Nome do artigo</th>
                                    <th scope="col">Codico</th>
                                  </tr>
                                </thead>
                                <tbody  class="listaTrAssoriar">
                                  
                                </tbody>
                              </table>
                        </div>
                        
                    </div>
                  </div>
                  <div hidden class="incluirCodigo" >
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #ed7800; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-5 "></div>
                        
                        
                        <form method="post" action="{{ route('created_categoria_save') }}">
                            @csrf
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Categoria</label>
                                    <input required  id="categoriaAtivo" name="categoriaAtivo"   class="form-control form-control-sm">
                                    <input required  id="qdtCodigo" name="qdtCodigo[]" type="hidden" value=""   class="form-control form-control-sm">
                                </div>        
                                <button style="background: #ed7800; width: 10pc;color: white" type="submit" class="btn"><i class="fas fa-plus-circle"></i> Guardar</button>
                            
                            </div>
                            <div class="col-md-12 mt-3">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nome do artigo</th>
                                        <th scope="col">codigo(s)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="listaArtigoInclusao">
                                    
                                    
                                    </tbody>
                                </table>
                            </div>
                        </form> 
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>

   
@endsection

@section('scripts')
    @parent
    <script>
        var listaCode=$(".listaCode");
        var listaTrAssoriar=$(".listaTrAssoriar");
        var listaArtigoInclusao=$(".listaArtigoInclusao");
        var arrayArtigos=[]
        var objectArtigos=null;
        var objectcodeCategory=null; 
        var qdtCodigo=$("#qdtCodigo");
        var qdt=null;
      $("#categoria").change(function(){
          $.ajax({
          url: "getCodeCategoria/" +  $("#categoria").val(),
          type: "GET",
          data: {
              _token: '{{ csrf_token() }}'
          },
          cache: false,
          dataType: 'json',
          success: function (response)
          {
              //$('#exampleModalCenter').modal('hide');
          }
          }).done(function(data)
          { 
              var tr=null;
              var i=0;
              if (data['data'].length>0) {
                  listaCode.empty();
                  
                  $.each(data['data'], function (index, item) { 
                      i++;
                      
                          tr+="<tr><td>"+i+"</td><td>"+item.nome_code+"</td><td>"+item.codeCat+"</td><td>"+item.nome_categoria+"</td><td>"+item.notaCode+"</td><td>"+item.nomeCriador+"</td><td>"+item.dataCriacao+"</td>"
                          tr+="</tr>"
                  });
              } else {
                  listaCode.empty();
                  tr+="<tr><td>Nenhum registo</td>"
                    tr+="</tr>"
                  
              }
              listaCode.append(tr);
          })
      })
      $(".list-group-item").click(function (){  
          var link= $(this).attr("data-link"); 
          if(link=="criarCategoria"){
            $(".criarCategoria").slideDown(1000)

            $(".criarCodigo").slideUp(1000)    
            $(".associarCodigo").slideUp(1000)
            $(".incluirCodigo").slideUp(1000)


          }
          else if(link=="criarCodigo"){
            $(".criarCategoria").slideUp(1000)
            $(".associarCodigo").slideUp(1000)
            $(".incluirCodigo").slideUp(1000)

            $(".criarCodigo").attr('hidden',false)
            $(".criarCodigo").slideDown(1000)
            
          }
          else if(link=="associarCodigo"){
            $(".criarCategoria").slideUp(1000)
            $(".criarCodigo").slideUp(1000)
            $(".incluirCodigo").slideUp(1000)

            $(".associarCodigo").attr('hidden',false)
            $(".associarCodigo").slideDown(1000)
            
          }
      })
      $("#incluirCodigo").click(function(){
        $(".criarCategoria").slideUp(1000)
        $(".criarCodigo").slideUp(1000)
        $(".associarCodigo").slideUp(1000)

        $(".incluirCodigo").attr('hidden',false)
        $(".incluirCodigo").slideDown(1000)
          var  tr=null;
          var  i=0;
          var  key= 0;
          var option_valor=null;
          console.log(objectcodeCategory)
          listaArtigoInclusao.empty();
            if (objectArtigos.length>0) {
                $.each(objectArtigos, function (index, item) {
                  
                  $.each(arrayArtigos, function (key,element) { 
                    // COMPARA SE OS VALORES SÃO IGUAIS
                    
                    if (item.id_artigo==element) {
                        option_valor=null;
                        // ATRIBUI AS OPÇÕES A VARIÁVEL
                        $.each(objectcodeCategory, function (chave, valor) { 
                            option_valor+="<option value='"+item.id_artigo+","+valor.id_code+"'>"+valor.code+"</option>"
                        });
                    }

                    if (item.id_artigo==element) {
                        i++;
                   qdtCodigo.val(i)
                      tr+="<tr><td>"+i+"</td><td>"
                        +item.nome+
                        "</td><td><select style='width:20pc'  id='codigo' name='codigo_"+i+"'   class='form-control form-control-sm col-3' data-actions-box='true' data-selected-text-format='count > 3' data-live-search='true'   required data-selected-text-format='values'  tabindex='-98'>"                        
                        +option_valor+
                        "</select></td>"
                      tr+="</tr>"
                    }
                  });  
              });
              } else {
                listaArtigoInclusao.empty();
                tr+="<tr><td>Nenhum registo</td>"
              tr+="</tr>"
            }
            listaArtigoInclusao.append(tr);

       

      })
      $("#codeInCategoria").change(function (e) { 
        $.ajax({
          url: "getCodeInCategoria/" + $("#codeInCategoria").val(),
          type: "GET",
          data: {
              _token: '{{ csrf_token() }}'
          },
          cache: false,
          dataType: 'json',
          success: function (response)
          {
              //$('#exampleModalCenter').modal('hide');
          }
          }).done(function(data)
          { 
            objectcodeCategory=data['data_code'];
            arrayArtigos=[];
            objectArtigos=data['data'];
            var  tr=null;
            var  i=0;
              if (data['data'].original==500) {
                alert("Está tabela não existe na plataforma")
              } else {
                listaTrAssoriar.empty();
                  if (data['data'].length>0) {
                      $.each(data['data'], function (index, item) { 
                        i++;
                        
                            tr+="<tr><td>"+i+"</td><td><input type='checkbox'  classs='checkArtigo' onclick='getArtigo("+item.id_artigo+")' name='artigo[]'></td><td>"+item.nome+"</td><td>"+item.nome_code+"</td>"
                            tr+="</tr>"
                    });
                  } else {
                    listaTrAssoriar.empty();
                     tr+="<tr><td>Nenhum registo</td>"
                    tr+="</tr>"
                  }
                  listaTrAssoriar.append(tr);
              }
          }) 
      });
      function getArtigo (artigo) {
        const found = arrayArtigos.find(element => element == artigo);
        if (found==undefined) {
           arrayArtigos.push(artigo) 
        } else {
          $.each(arrayArtigos, function (index, item) { 
            if(item==artigo){
              arrayArtigos.splice([index],1)
            }    
          });
         
          
        }
        
        
      };
    </script>
@endsection