@php use App\Modules\Reports\Controllers\DocsReportsController; @endphp
<br>
<div class="container-fluid" style="padding:0;">
    <div class="row">
        <div class="col-md-12">
                        <style>
                                .tabela_principal{ height: 2px; padding: 0; margin: 0; margin-bottom: 1px; }
                                    .tabela_principal td{ background-color:transparent;font-family: calibri light; }
                                    .tabela_principal thead{border-top: none; border:1px solid  #2c2c2c; padding: 0; color: #fff;}
                                    .tabela_principal thead th{background-color: #2c2c2c; border:1px solid #fff; width: 800px; text-align: center; padding:0px; padding-left:1px;}
                         
                             .tfoot {
                              border-bottom: 1px solid #BCBCBC !important;
                              text-align: right;
                          }
                      </style>
            @php $total = 0; $linha=0; @endphp
            <table class="table  tabela_principal" style="width:100%;">
                <thead  class="">
                    <th  style="font-size:18px; text-align: center;width:4pc;">#</th>
                    <th  style="font-size:18px; text-align: center;width: 200px!important;">Nº Matricula </th>
                    <th  style="font-size:18px; text-align: center;width: 360px!important;">Estudante </th>
                    <th  style="font-size:18px; text-align: center;width: 360px!important;">Curso </th>
                    <th  style="font-size:18px; text-align: center;width: 100px!important;">Turma </th>
                </thead>   
                <tbody>
                    @php( $i =1)
                    @foreach ($emoluments as $item)

                    @if (isset($item->meca))
                        
                  
                    <tr>
                        <td style="font-size:16px;">{{$i++}}</td>
                        <td style="font-size:16px;">{{$item->meca}}</td>
                        <td style="font-size:16px;">{{$item->name}}</td>
                        <td style="font-size:16px;">{{$item->course_name}}</td>
                        <td style="font-size:16px;">{{DocsReportsController::getTurma($item->id,$item->lective_year,$item->year)}}</td> 
                    </tr>  @endif
                    @endforeach
                </tbody>
            </table>
        </div>
</div>
