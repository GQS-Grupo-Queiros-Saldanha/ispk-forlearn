@section('title',__('EVENTOS'))
@extends('web_site.website_layout')


@section('content')

    <div id="eventos">
    
        <section class="breadcrumbs">
          <div class="container">
    
            <div class="d-flex justify-content-between align-items-center">
              <h2>Eventos</h2>
              <!-- <ol>
                <li><a href="index.html">Home</a></li>
                <li>Our Services</li>
              </ol> -->
            </div>
    
          </div>
        </section><!-- End Our Services Section -->
    
        <!-- ======= Service Details Section ======= -->
        <section class="service-details">
          <div class="container">
    
            <div class="row">
              <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                <div class="card">
                  <div class="card-img">
                    <img src="{ asset('css/web_site_style/assets/img/service-details-1.jpg') }}" alt="...">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title"><a href="#">Evento 1</a></h5>
                    <p class="card-text">Lorem ipsum dolor sit amet, consectetur elit, sed do eiusmod tempor ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</p>
                    <div class="read-more"><a href="#"><i class="bi bi-arrow-right"></i>Ler mais</a></div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                <div class="card">
                  <div class="card-img">
                    <img src="{ asset('css/web_site_style/assets/img/service-details-2.jpg') }}" alt="...">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title"><a href="#">Evento 2</a></h5>
                    <p class="card-text">Sed ut perspiciatis unde omnis iste natus error sit voluptatem doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo</p>
                    <div class="read-more"><a href="#"><i class="bi bi-arrow-right"></i> Ler mais</a></div>
                  </div>
                </div>
    
              </div>
              <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                <div class="card">
                  <div class="card-img">
                    <img src="{ asset('css/web_site_style/assets/img/service-details-3.jpg') }}" alt="...">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title"><a href="#">Evento 3</a></h5>
                    <p class="card-text">Nemo enim ipsam voluptatem quia voluptas sit aut odit aut fugit, sed quia magni dolores eos qui ratione voluptatem sequi nesciunt Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet</p>
                    <div class="read-more"><a href="#"><i class="bi bi-arrow-right"></i> Ler mais</a></div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                <div class="card">
                  <div class="card-img">
                    <img src="{ asset('css/web_site_style/assets/img/service-details-4.jpg') }}" alt="...">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title"><a href="#">Evento 4</a></h5>
                    <p class="card-text">Nostrum eum sed et autem dolorum perspiciatis. Magni porro quisquam laudantium voluptatem. In molestiae earum ab sit esse voluptatem. Eos ipsam cumque ipsum officiis qui nihil aut incidunt aut</p>
                    <div class="read-more"><a href="#"><i class="bi bi-arrow-right"></i> Ler mais</a></div>
                  </div>
                </div>
              </div>
            </div>
    
          </div>
        </section><!-- End Service Details Section -->
    
    </div>

@endsection

@section('scripts')
    @parent
    
    <script>
        var seletor=$("#eventos");
        var posicao=$(seletor).offset().top;
        $("html, body").animate({scrollTop: posicao - 98})
    </script>
    

@endsection