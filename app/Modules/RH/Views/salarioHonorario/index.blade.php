@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button{
            border: none; background: none; outline-style: none;transition: all 0.5s;
        }
        .list-group li button:hover{cursor: pointer;font-size: 15px;transition: all 0.5s; font-weight: bold }
        .subLink{
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }
        .subLink:hover{
            cursor: pointer;font-size: 15px;transition: all 0.5s; border-bottom: #dfdfdf 1px solid;
        }

    </style>
    <div class="content-panel">
        <div class="content-header">
            @include('RH::index_menu')
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                      <h1>{{$action}}</h1> 
                    </div>
                    <div class="col-sm-6">
                      
                    </div>
                </div>
            </div>
        </div>
        <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                 @include('RH::index_menuSalario')

                <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div  class="criarCodigo ">
                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                            <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px "
                                class="col-12 m-0 mb-4 "></div>
                            <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase"></h5>

                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
       
    </div>
    
@endsection
@section('scripts')
@parent

@endsection