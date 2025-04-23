 @section('title',__('Criar Plano de Estudos Avaliação'))


 @extends('layouts.backoffice')

 @section('content')
 <div class="content-panel">
     <div class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1 class="m-0 text-dark">
                         Plano de Estudos e Avaliação
                     </h1>
                 </div>
                 <div class="col-sm-6">

                 </div>
             </div>
         </div>
     </div>

     {{-- Main content --}}
     <div class="content" style="margin-bottom: 10px">
         <div class="container-fluid">

             {!! Form::open(['route' => ['spa.edit']]) !!}

             <div class="row">
                 <div class="col">
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

                     <button type="submit" class="btn btn-sm btn-success mb-3">
                         <i class="fas fa-save"></i>
                         Guardar
                     </button>

                     <div class="card">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group col">
                                     <label>Avaliação</label>
                                     <select name="avaliacao_id" id="" class="form-control">
                                         @foreach ($model as $item)
                                         <option value="{{$item->plano_estudo_avaliacaos_id }}" selected>
                                            {{$item->avaliacao_nome}}
                                         </option>
                                         @endforeach
                                        @foreach ($avaliacaos as $avl)
                                            <option value="{{ $avl->avaliacao_id}}">
                                                {{$avl->avaliacao_nome}}
                                            </option>
                                        @endforeach
                                         
                                     </select>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group col">
                                     <label>Plano de Estudos</label>
                                     <select name="study_plans_id" id="" class="form-control">
                                        @foreach ($model as $item)
                                            <option value="{{$item->sp_id}}" selected>
                                                {{$item->spt_nome}}
                                            </option>
                                        @endforeach
                                        @foreach ($plano_estudos as $pe)
                                            <option value="{{$pe->study_plans_id }}">
                                                {{$pe->spt_display_name}}
                                            </option>
                                        @endforeach 

                                     </select>
                                 </div>
                             </div>

                         </div>
                     </div>
                     <hr>

                     {!! Form::close() !!}

                 </div>
             </div>
         </div>
         @endsection

         @section('scripts')
         @parent
         <script>

         </script>
         @endsection
