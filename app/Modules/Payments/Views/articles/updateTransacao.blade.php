{{-- @section('title',__('Payments::articles.articles'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content') --}}
{{-- <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script> --}}

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- <a href="{{ route('articles.index') }}" style="margin-left: 400px;margin-top: 20pc;padding-top: 20px">Emolumentos</a>  --}}
                     </div>
                </div>
            </div>
        </div>
        {{-- @if ($errors->any())
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
        @endif --}}
            
        <div class="content col-12">
            {{-- <div class="container-fluid col-12">
                <div class="row col-12" id="processoCriacao">
                    <div  class="col-6">
                        <form method="POST" action="{{ route('updateTransainEstorno') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary mb-3">Selecionar tudo</button>
                    </div>
                </div>

                
                <div class="container-fluid mt-4">
                    <table class="table table-striped">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">#</th>
                            <th scope="col">MATRICULA</th>
                            <th scope="col">ALUNO</th>
                            <th scope="col">EMOLUMENTO/PROPINA</th>
                            <th scope="col">bank</th>
                            <th scope="col">REFERÊNCIA</th>
                            <th scope="col">MÊS</th>
                            <th scope="col">VALOR</th>
                            <th scope="col">SALDO EM CARTEIRA</th>
                            <th scope="col">CRIADO POR</th>
                            <th scope="col">DATA</th>
                            <th scope="col">RECIBO</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">RECIBOS DUPLICADOS</th>
                          </tr>
                        </thead>
                        @php
                            $i=0;
                        @endphp
                        <tbody id="listaItem">
                            @foreach ($getTransaction as $item)
                            @php $i++; @endphp
                            @if ($item->recibo_repetido!="N/A")
                                <tr>
                                    <td>{{$item->transaction_id}}</td>
                                    <td><input type="checkbox" name="" id=""></td>
                                    <td>{{$item->matriculation_number}}</td>
                                    <td>{{$item->full_name}}</td>
                                    <td>{{$item->article_name}}</td>
                                    <td>{{$item->bank_name}}</td>
                                    <td>{{$item->reference}}</td>
                                    <td>{{$item->month==null?"N/A":$item->month}}</td>
                                    <td>{{$item->valorreferencia==0?"0":$item->valorreferencia}}</td>
                                    <td>{{$item->valorSaldo_credit==null?"N/A":$item->valorSaldo_credit}}</td>
                                    <td>{{$item->created_by_user}}</td>
                                    <td>{{$item->created_atranst}}</td>
                                    <td>{{$item->recibo==null?"N/A":$item->recibo}}</td>
                                    <td>{{$item->status}}</td>
                                    <td>{{$item->recibo_repetido}}</td>
                                    <input type="hidden" name="id_trasancao[]" value="{{$item->transaction_id}}">
                                </tr>
                            @endif
                               
                            @endforeach
                        </form>
                        </tbody>
                      </table>
                </div>  
            </div> --}}


            <style>
                #customers {
                  font-family: Arial, Helvetica, sans-serif;
                  border-collapse: collapse;
                  width: 100%;
                }
                
                #customers td, #customers th {
                  border: 1px solid #ddd;
                  padding: 8px;
                }
                
                #customers tr:nth-child(even){background-color: #f2f2f2;}
                
                #customers tr:hover {background-color: #ddd;}
                
                #customers th {
                  padding-top: 12px;
                  padding-bottom: 12px;
                  text-align: left;
                  background-color: #04AA6D;
                  color: white;
                }
                </style>




            <table id="customers">
                  <tr>
                    <th>#</th>
                    <th>Estudante</th>
                    <th>e-mail</th>
                    <th>Saldo em carteira</th>
                  </tr>
                <tbody>
                    @php
                        $i=0;
                    @endphp
                    @foreach ($getAll_studentMore_credit_saldo as $item)
                    @php
                        $i++;
                    @endphp
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{number_format($item->credit_balance,2,",",".")}}</td>
                        </tr>
                    @endforeach
                  
                </tbody>
              </table>  
              
             
                



        </div>
    </div>
{{-- @endsection

@section('scripts')
    @parent
@endsection --}}



