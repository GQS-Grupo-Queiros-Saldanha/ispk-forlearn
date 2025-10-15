@section('title',__('RH-recurso humanos'))
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<style>
    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }
</style>
<div class="content-panel">
    @include('RH::index_menu')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1>{{$data['action']}}</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>
    <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
    <div class="content-fluid ml-4 mr-4 mb-5">
        <div class="d-flex align-items-start">
            @include('RH::index_menuStaff')
            <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col"
                id="v-pills-tabContent">

                <div class="associarCodigo">
                    <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                        <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-4 "></div>
                        
                        <h5 class="col-md-12 mb-4 text-right text-muted text-uppercase"><i class="fas fa-user-plus"></i> Criar funcionario</h5>
                        {{-- formularios --}}
                        <div class="col-12 mb-4 border-bottom">
                            <form class="pb-4">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Email</label>
                                        <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Password</label>
                                        <input type="password" class="form-control" id="inputPassword4"
                                            placeholder="Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputAddress">Address</label>
                                    <input type="text" class="form-control" id="inputAddress"
                                        placeholder="1234 Main St">
                                </div>
                                <div class="form-group">
                                    <label for="inputAddress2">Address 2</label>
                                    <input type="text" class="form-control" id="inputAddress2"
                                        placeholder="Apartment, studio, or floor">
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputCity">City</label>
                                        <input type="text" class="form-control" id="inputCity">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputState">State</label>
                                        <select id="inputState" class="form-control">
                                            <option selected>Choose...</option>
                                            <option>...</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputZip">Zip</label>
                                        <input type="text" class="form-control" id="inputZip">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gridCheck">
                                        <label class="form-check-label" for="gridCheck">
                                            Check me out
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Sign in</button>
                            </form>
                        </div>

                        <div class="col-md-5 ">
                            <form>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Categoria</label>
                                    <select id="codeInCategoria" name="categoria"
                                        class="selectpicker form-control form-control-sm" data-actions-box="true"
                                        data-selected-text-format="count > 3" data-live-search="true" required
                                        data-selected-text-format="values" tabindex="-98">
                                        <option selected></option>
                                    </select>
                                </div>
                                <button id="incluirCodigo" style="background: #7eaf3e; width: 10pc;color: white"
                                    type="button" class="btn"><i class="fas fa-pen-to-square"></i> Editar </button>
                            </form>
                        </div>
                        {{-- charts --}}
                        <div class="col-md-7 mt-3 row pr-0">
                            <div class="col mb-2 pl-0 mr-0">
                                <div class="p-0 small-box bg-success text-white rounded">
                                    <div class="row pl-2 pt-2">
                                        <div class="inner col-md-9">
                                            <h2 id="totalLiuidade">590</h2>
                                            <p>PROPINAS LIQUIDADOS</p>
                                        </div>
                                        <div class="icon">
                                            <i style="font-size: 4pc; opacity: 0.2;" class="fa-solid fa-receipt"></i>
                                        </div>
                                    </div>
                                    <div style="background: #46434329" class="mt-3 mb-0 pb-0">
                                        <p href="#" class="small-box-footer text-center">Actúalizado a: @php
                                            $data=date('Y-m-d H:i:s'); echo
                                            "$data"; @endphp <i class="fas fa-date"></i></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-2 pl-0 mr-0 pr-0">
                                <div class="p-0 small-box bg-success text-white rounded">
                                    <div class="row pl-2 pt-2">
                                        <div class="inner col-md-9">
                                            <h2 id="totalLiuidade">590</h2>
                                            <p>PROPINAS LIQUIDADOS</p>
                                        </div>
                                        <div class="icon">
                                            <i style="font-size: 4pc; opacity: 0.2;" class="fa-solid fa-receipt"></i>
                                        </div>
                                    </div>
                                    <div style="background: #46434329" class="mt-3 mb-0 pb-0">
                                        <p href="#" class="small-box-footer text-center">Actúalizado a: @php
                                            $data=date('Y-m-d H:i:s'); echo
                                            "$data"; @endphp <i class="fas fa-date"></i></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="card card-widget widget-user ">
                                <div class="widget-user-header bg-info ">
                                    <div class="m-3">
                                        <h4 class="widget-user-username">Alexander Pierce</h4>
                                        <h6 class="widget-user-desc">Founder &amp; <b>CEO</b></h5>
                                    </div> 
                                </div>
                                <div class="widget-user-image text-center">
                                    <i class="fas fa-user mt-1 mb-1" style="font-size: 5pc"></i>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">3,200</h5>
                                                <span class="description-text">SALES</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">13,000</h5>
                                                <span class="description-text">FOLLOWERS</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header">35</h5>
                                                <span class="description-text">PRODUCTS</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-7 pl-0">
                            <canvas class="my-4 w-100 chartjs-render-monitor m-0 p-0"  id="myChart" width="1600" height="500" style="display: block; width: 1096px; height: 454px;"></canvas>
                        </div>
                        

                        <div class="mt-5 col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">First</th>
                                        <th scope="col">Last</th>
                                        <th scope="col">Handle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>gelsonde </td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Jacob</td>
                                        <td>Thornton</td>
                                        <td>@fat</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td>Larry</td>
                                        <td>the Bird</td>
                                        <td>@twitter</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>

@endsection
@section('scripts')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
  var ctx = document.getElementById("myChart");
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ["outubro", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
      datasets: [{
        data: [54, 21345, 18483, 24003, 23489, 24092, 12034],
        lineTension: 0,
        backgroundColor: '#e8f0fe',
        borderColor: '#007bff',
        borderWidth: 2,
        pointBackgroundColor: '#007bff'
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: false
          }
        }]
      },
      legend: {
        display: false,
      }
    }
  });
</script>
@endsection