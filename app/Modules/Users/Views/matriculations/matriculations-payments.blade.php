@extends('layouts.generic_index_new')
@php
    $totalPendentes = 0;
    $totalLiuidade = 0;
    $otal = 0;
@endphp
@section('title',__('Matrículas - Propinas'))
@section('page-title', 'Estado de Mensalidade')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Matrículas - Propinas</li>
@endsection
@section('styles-new')
    <style>
        .divtable::-webkit-scrollbar {
            width: 8px;
            height: 2px;
            border-radius: 30px;
            box-shadow: inset 20px 20px 60px #bebebe,
                inset -20px -20px 60px #ffffff;
        }

        .divtable::-webkit-scrollbar-track {
            background: #e0e0e0;
            box-shadow: inset 20px 20px 60px #bebebe,
                inset -20px -20px 60px #ffffff;
            border-radius: 30px;
            height: 2px
        }

        .divtable::-webkit-scrollbar-thumb {
            background-color: #343a40;
            border-radius: 30px;
            border: none;
            height: 2px
        }

        .divtable {
            height: 90vh
        }
    </style>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
  <div style="z-index: 1900" class="modal fade modalLoad"   tabindex="-1" aria-labelledby="staticBackdropLabel"  data-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <i style="margin-left: 12pc; font-size: 8pc; color:#cae6f3;" class="fa fa-circle-notch fa-spin"></i>
    </div>
  </div>

  <div class="content">
      <div class="d-flex gap-1">
        <div>
          <a target="_back" href="" id="gerarPDF" class="ml-4 btn btn-success">Gerar PDF</a>
        </div>
        <div>
          <button value="line" class="ml-4 btn btn-sm text-white p-2 pr-3 pl-3 btn-grafic "
            style="background:  #d9c617; width: 50px;">L</button>
        </div>
        <div>
          <button value="bar" class="ml-4 btn btn-sm text-white p-2 pr-3 pl-3 btn-grafic"
            style="background:  #ebd507; width: 50px;">C</button>
        </div>

      </div>
      @php $totalPendentes=0;$totalLiuidade=0;$otal=0 @endphp
  </div>

  <div id="canvasgrafic" class="container-fluid">
    <canvas class="my-2 w-100 chartjs-render-monitor" id="myChart" width="2000" height="500"
      style="display: block; width: 1076px; height: 454px;"></canvas>
  </div>
  
  <hr class="mt-2">
  
  <div class="container-fluid col-12 mt-3 mb-4">
    <h3>Painel da Mensalidade</h3>
    <div class="mb-2" style="background: #00d55a;padding: 3px ; border-top-left-radius: 1pc ;border-top-right-radius: 1pc"></div>
    <div class="row">
      <div class="p-0 pl-3 col-4">
        <div class="m-0 p-0">
          <div class="col mb-2 pl-0 mr-0">
            <div class="p-0 small-box bg-info rounded">
              <div class="row pl-2 pt-2">
                <div class="inner col-md-9">

                  <h2 class="text-white" id="totalPendentes"><b>{{$totalPendentes}}</b></h2>
                  <p>PROPINAS PENDENTES</p>
                </div>
                <div class="icon">
                  <i style="font-size: 4pc; opacity: 0.2;" class="fa-solid fa-cash-register"></i>
                </div>
              </div>
              <div style="background: #46434329" class="mt-3 mb-0 pb-0 text-center">
                <a target="_blank" href="{{route('generate-enrollment-pending') }}" class="small-box-footer ">Lista de
                  pendentes <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>

          <div class="col mb-2 pl-0 mr-0">
            <div class="p-0 small-box bg-success text-white rounded">
              <div class="row pl-2 pt-2">
                <div class="inner col-md-9">
                  <h2 id="totalLiuidade">{{$totalLiuidade}}</h2>
                  <p>PROPINAS LIQUIDADOS</p>
                </div>
                <div class="icon">
                  <i style="font-size: 4pc; opacity: 0.2;" class="fa-solid fa-receipt"></i>
                </div>
              </div>
              <div style="background: #46434329" class="mt-3 mb-0 pb-0">
                <p href="#" class="small-box-footer text-center">Actúalizado a: @php $data=date('Y-m-d H:i:s'); echo
                  "$data"; @endphp <i class="fas fa-date"></i></p>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="divtable table-responsive col-8">
        <table class="table_te" style="width:100%;background-color: #F5F3F3;color:#000;">
          <tr>
            <th class="text-center" colspan="2">
              <h5 class="mt-2">QUADRO RESUMO DAS MENSALIDADES</h5>
            </th>
          </tr>
        </table>
        <div>
          <table class="table_te mt-1 mb-1" style="width: 100%;">
            <thead style="background-color: #F5F3F3; width: 100%;">
              {{-- <tr class="col-12">
                <td style="width: 100%;border-right: white 8px solid;" colspan="3">
                  <h6 class="text-center mt-2">QUADRO MENSALIDADE</h6>
                </td>
                <td>TOTAL</td>
              </tr> --}}

            </thead>
            <thead>
              <tr>
                <th class="pl-2" style="font-size: 11pt;">MESES</th>
                <th class="text-center" style="font-size: 10pt;">PENDENTE</th>
                <th class="text-center" style="font-size: 10pt;">LIQUIDADO</th>
                <th class="text-center" style="font-size: 10pt;">TOTAL</th>
              </tr>
            </thead>
            <tbody id="lista-mensalidade">
              @if ($getlectivoPresent[0]->display_name=='20/21')
              @php $somaPedente=0;$somaLiquidade=0 @endphp
              <tr style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                <td class="text-center" style=" border-right: white 1px solid;">Março</td>
                <td class="text-center">
                  @foreach ($getMonthEmolument as $matricula)
                    @foreach ($matricula as $curso)
                      @foreach ($curso as $nome_student)
                        @foreach ($nome_student as $article)
                          @foreach ($article as $element)
                            @if ($element->article_month==3 and $element->article_year==2020)
                              @if ($element->status=='pending' || $element->status=='partial')
                                @php $somaPedente+=1 @endphp
                              @elseif($element->status=='total')
                                @php $somaLiquidade+=1 @endphp
                              @endif
                            @endif
                          @endforeach
                        @endforeach
                      @endforeach
                    @endforeach
                  @endforeach
                  @php $totalPendentes+=$somaPedente @endphp
                  @php $totalLiuidade+=$somaLiquidade @endphp
                  {{$somaPedente}}
                </td>
                <td style="border-right: white 8px solid;" class="text-center">
                  {{$somaLiquidade}}
                </td>
                <td class="text-center">
                  @php $total=$somaPedente+$somaLiquidade @endphp
                  {{$total}}
                </td>
              </tr>
              @else
              @endif

              @foreach($ordem_Month as $item)
              @php $somaPedente=0;$somaLiquidade=0 @endphp
              <tr style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                <td class="pl-2" style=" border-right: white 1px solid;">{{$item['display_name']}}</td>
                <td style=" border-right: white 1px solid;" class="text-center">
                  @foreach ($getMonthEmolument as $matricula)
                    @foreach ($matricula as $curso)
                      @foreach ($curso as $nome_student)
                        @foreach ($nome_student as $article)
                          @foreach ($article as $element)
                            @if ($element->status=='pending' || $element->status=='partial')
                              @if ($item['id']==$element->article_month and $element->article_month!=3 and
                                $element->article_year!=2020)
                                @php $somaPedente+=1 @endphp
                              @elseif($item['id']==$element->article_month and $element->article_month==3 and
                                $element->article_year!=2020)
                                @php $somaPedente+=1 @endphp
                              @elseif($item['id']==$element->article_month and $element->article_month!=3 and
                                $element->article_year==2020)
                                @php $somaPedente+=1 @endphp
                              @endif
                            @elseif($element->status=='total')
                              @if ($item['id']==$element->article_month and $element->article_month!=3 and
                                $element->article_year!=2020)
                                @php $somaLiquidade+=1 @endphp
                              @elseif($item['id']==$element->article_month and $element->article_month==3 and
                                $element->article_year!=2020)
                                @php $somaLiquidade+=1 @endphp
                              @elseif($item['id']==$element->article_month and $element->article_month!=3 and
                                $element->article_year==2020)
                                @php $somaLiquidade+=1 @endphp
                              @endif
                            @endif
                          @endforeach
                        @endforeach
                      @endforeach
                    @endforeach
                  @endforeach
                  @php $totalPendentes+=$somaPedente @endphp
                  @php $totalLiuidade+=$somaLiquidade @endphp
                  {{$somaPedente}}
                </td>
                <td style="border-right: white 8px solid;" class="text-center">
                  {{$somaLiquidade}}
                </td>
                <td class="text-center">
                  @php $total=$somaPedente+$somaLiquidade @endphp
                  {{$total}}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts-new')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        // variaveis
        var setMonth = @json($ordem_Month);
        var getMonth = JSON.parse(JSON.stringify(setMonth));
        var setEmolument = @json($emoluments);
        var getEmolument = JSON.parse(JSON.stringify(setEmolument));
        var ctx = document.getElementById("myChart");
        var vetorMonth = [];
        var vetorEmolument = [];
        var vetorArticl = [];
        var articlAtivo = null;
        var getMonthLiquidado = [];
        var getMonthPendent = [];
        var somaLiquidade = 0;
        var somaPendent = 0;
        var vetorQtdMonthLiquidade = [];
        var vetorQtdMonthPendent = [];
        var getBtn_grafic = 'line';
        var line_datasets = null;
        var vetor = $("#lective_years").val()
        var lista_mensalidade = $("#lista-mensalidade");
        vetor = vetor.split(",")
        var gerArquivo = document.getElementById("gerarPDF");
        gerArquivo.href = '/users/getMatriculations-paymentsgerarPdf/' + vetor[0];
        // 
        let totalPendentes = '{{ $totalPendentes }}'
        $("#totalPendentes").html("<b>" + totalPendentes + "</b>");
        let totalLiuidade = '{{ $totalLiuidade }}'
        $("#totalLiuidade").html("<b>" + totalLiuidade + "</b>");

        $(".btn-grafic").click(function(e) {
            var getThis = $(this).val();
            getBtn_grafic = String(getThis)
            getGrafic(String(getBtn_grafic));

        });

        $.each(getEmolument, function(index, value) {
            $.each(value, function(key, element) {
                $.each(element, function(chave, item) {
                    $.each(item, function(setKey, getElement) {
                        $.each(getElement, function(set, get) {
                            if (vetorArticl.length == 0) {
                                vetorArticl.push(get.id_article_requests)
                                articlAtivo = true
                            } else {
                                var found = vetorArticl.find(elementget =>
                                    elementget == get.id_article_requests)
                                if (found == undefined) {
                                    vetorArticl.push(get.id_article_requests);
                                    articlAtivo = true

                                } else {
                                    articlAtivo = false
                                }
                            }

                            if (articlAtivo == true) {
                                if (get.status == "total") {
                                    getMonthLiquidado.push({
                                        month: get.article_month
                                    })
                                } else if (get.status == "pending") {
                                    getMonthPendent.push({
                                        month: get.article_month
                                    })
                                } else if (get.status == "partial") {
                                    getMonthPendent.push({
                                        month: get.article_month
                                    })
                                }
                            }

                        });
                    });
                });
            });
        });

        // calcular meses liquidados e // calcular meses pedentes
        $.each(getMonth, function(index, element) {
            somaLiquidade = 0
            somaPendent = 0
            $.each(getMonthLiquidado, function(key, item) {
                if (element.id == item.month) {
                    somaLiquidade += 1
                }
            });

            $.each(getMonthPendent, function(key, item) {
                if (element.id == item.month) {
                    somaPendent += 1
                }
            });
            vetorQtdMonthPendent.push(somaPendent)
            vetorQtdMonthLiquidade.push(somaLiquidade)
        });

        $.each(getMonth, function(index, element) {
            vetorMonth.push(element.display_name)
        });
        getDataGrafic(vetorQtdMonthLiquidade, vetorQtdMonthPendent, vetorMonth)

        function getDataGrafic(vetorQtdMonthLiquidade, vetorQtdMonthPendent, vetorMonth) {
            line_datasets = {
                labels: vetorMonth,
                datasets: [{
                        label: 'PROPINAS LIQUIDADOS',
                        data: vetorQtdMonthLiquidade,
                        lineTension: 0.4,
                        backgroundColor: 'transparent',
                        borderColor: '#38c172',
                        borderWidth: 3,
                        pointBackgroundColor: '#79f5ae'
                    },
                    {
                        label: 'PROPINAS PENDENTES',
                        data: vetorQtdMonthPendent,
                        lineTension: 0.4,
                        backgroundColor: 'transparent',
                        borderColor: '#007bff',
                        borderWidth: 3,
                        pointBackgroundColor: '#d3e8ff'
                    },
                ]
            }
        }

        getGrafic()

        function getGrafic() {
            var myChart = new Chart(ctx, {
                type: getBtn_grafic,
                data: line_datasets,
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
                            }
                        }]
                    },
                    legend: {
                        display: true,
                    }
                }
            });
        }

        $("#lective_years").change(function(e) {
            var lesctivo = $(this).val();
            var vetor = lesctivo.split(',')
            gerArquivo.href = '/users/getMatriculations-paymentsgerarPdf/' + vetor[0];
            $.ajax({
                url: "/users/getMatriculations-paymentsAlectivo/" + vetor[0],
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $(".modalLoad").modal('show')
                }
            }).done(function(data) {
                if (vetor[1] == '20/21') {
                    getMonth.unshift({
                        id: 3,
                        display_name: "Março",
                        year: 2020
                    })
                    upGrafic(data['data'], getMonth)
                    created_tableAnolectivo(data['data'], vetor[1], getMonth)
                } else {
                    setMonth = @json($ordem_Month);
                    getMonth = JSON.parse(JSON.stringify(setMonth));
                    upGrafic(data['data'], getMonth)
                    created_tableAnolectivo(data['data'], vetor[1], getMonth)
                }

                setTimeout(() => {
                    $(".modalLoad").modal('hide');
                }, 2000);

            });
        });

        function upGrafic(data, getMonth) {
            vetorMonth = [];
            getMonthLiquidado = [];
            getMonthPendent = [];
            somaLiquidade = 0;
            somaPendent = 0;

            var somaLiquidade2020 = 0;
            var somaPendent2020 = 0;
            vetorQtdMonthLiquidade = [];
            vetorQtdMonthPendent = [];
            vetorArticl = [];
            var getMonth2020Liquidade = [];
            var getMonth2020Pendent = [];
            articlAtivo = null;
            $.each(data, function(index, value) {
                $.each(value, function(key, element) {
                    $.each(element, function(chave, item) {
                        $.each(item, function(setKey, getElement) {
                            $.each(getElement, function(set, get) {
                                if (vetorArticl.length == 0) {
                                    vetorArticl.push(get.id_article_requests)
                                    articlAtivo = true
                                } else {
                                    var found = vetorArticl.find(elementget =>
                                        elementget == get.id_article_requests)
                                    if (found == undefined) {
                                        vetorArticl.push(get.id_article_requests);
                                        articlAtivo = true

                                    } else {
                                        articlAtivo = false
                                    }
                                }

                                if (articlAtivo == true) {
                                    if (get.article_month == 3 && get
                                        .article_year == 2020) {
                                        if (get.status == "total") {
                                            getMonth2020Liquidade.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        } else if (get.status == "pending") {
                                            getMonth2020Pendent.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        } else if (get.status == "partial") {
                                            getMonth2020Pendent.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        }


                                    } else {
                                        if (get.status == "total") {
                                            getMonthLiquidado.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        } else if (get.status == "pending") {
                                            getMonthPendent.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        } else if (get.status == "partial") {
                                            getMonthPendent.push({
                                                month: get.article_month,
                                                year: get.article_year
                                            })
                                        }
                                    }

                                }

                            });
                        });
                    });
                });
            });
            somaLiquidade2020 = getMonth2020Liquidade.length;
            somaPendent2020 = getMonth2020Pendent.length;
            // calcular meses liquidados e // calcular meses pedentes
            $.each(getMonth, function(index, element) {
                somaLiquidade = 0
                somaPendent = 0
                somaLiquidade2020 = 0;
                somaPendent2020 = 0;
                $.each(getMonthLiquidado, function(key, item) {
                    if (element.id == item.month) {

                        somaLiquidade += 1

                    }
                });

                $.each(getMonthPendent, function(key, item) {
                    if (element.id == item.month) {
                        somaPendent += 1
                    }
                });

                vetorQtdMonthPendent.push(somaPendent)
                vetorQtdMonthLiquidade.push(somaLiquidade)

            });
            if (getMonth2020Liquidade.length > 0) {
                vetorQtdMonthLiquidade.unshift(getMonth2020Liquidade.length)
                vetorQtdMonthPendent.unshift(getMonth2020Pendent.length)
            }
            $.each(getMonth, function(index, element) {
                vetorMonth.push(element.display_name)
            });
            getDataGrafic(vetorQtdMonthLiquidade, vetorQtdMonthPendent, vetorMonth)
            getGrafic()
        }

        // FUNCÃO QUE REALIZA OS CALCULO PARA A TABELA
        function created_tableAnolectivo(data, anoLectivo, getMonth) {
            var tr = null;
            var vetorArticl = [];
            var articlAtivo = null;
            var somaPedente = 0;
            var somaLiquidade = 0;
            var total = 0;
            lista_mensalidade.empty();
            totalPendentes = 0;
            totalLiuidade = 0;
            anoLectivo == '20/21' ? $("#marco2020").attr('hidden', false) : $("#marco2020").attr('hidden', true);
            $.each(getMonth, function(getIndex, getItem) {
                somaPedente = 0;
                somaLiquidade = 0;
                total = 0;
                tr +=
                    '<tr style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px" ><td class="pl-2"  style=" border-right: white 1px solid;">' +
                    getItem.display_name + '</td>'
                $.each(data, function(matricula, value) {
                    $.each(value, function(curso, element) {
                        $.each(element, function(nameStudent, valueElement) {
                            $.each(valueElement, function(chave, article_user) {
                                $.each(article_user, function(key, item) {
                                    if (getItem.year == '2020') {
                                        if (item.article_year == '2020' &&
                                            item.article_month == 3 && item
                                            .status ==
                                            "pending" || getItem.id == item
                                            .article_month && item.status ==
                                            "partial") {
                                            somaPedente += 1
                                        } else if (item.article_year ==
                                            '2020' && item.article_month ==
                                            3 && item
                                            .status == "total") {
                                            somaLiquidade += 1
                                        }
                                    } else {
                                        if (getItem.id == item
                                            .article_month && item.status ==
                                            "pending" || getItem
                                            .id == item.article_month &&
                                            item.status == "partial") {
                                            somaPedente += 1
                                        } else if (getItem.id == item
                                            .article_month && item.status ==
                                            "total") {
                                            somaLiquidade += 1
                                        }
                                    }
                                });
                            });
                        });
                    });
                });
                totalPendentes += somaPedente;
                totalLiuidade += somaLiquidade;
                total = somaLiquidade + somaPedente
                tr += '<td style=" border-right: white 1px solid;" class="text-center">' + somaPedente + '</td>'
                tr += '<td style=" border-right: white 3px solid;" class="text-center">' + somaLiquidade + '</td>'
                tr += '<td class="text-center">' + total + '</td>'
                tr += '</tr>'
            });

            lista_mensalidade.append(tr);
            $("#totalLiuidade").text(null);
            $("#totalPendentes").text(null);
            $("#totalLiuidade").html("<b>" + totalLiuidade + "</b>");
            $("#totalPendentes").html("<b>" + totalPendentes + "</b>");

        }
    </script>
@endsection
