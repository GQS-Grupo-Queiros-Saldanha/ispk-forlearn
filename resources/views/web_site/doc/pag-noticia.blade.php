@section('title',__('NOTÍCIAS'))
@extends('web_site.website_layout')

@section('content')

    <div id="noticia">
         <!-- ======= Our Portfolio Section ======= -->
         <section class="breadcrumbs">
          <div class="container">
    
            <div class="d-flex justify-content-between align-items-center">
              <h2>Notícias</h2>
            </div>
    
          </div>
        </section><!-- End Our Portfolio Section -->
    
        <!-- ======= Portfolio Section ======= -->
        <section class="portfolio">
          <div class="container">
    
            <div class="row">
              <div class="col-lg-12">
                <ul id="portfolio-flters">
                  <li data-filter="*" class="filter-active">All</li>
                  <li data-filter=".filter-app">App</li>
                  <li data-filter=".filter-card">Card</li>
                  <li data-filter=".filter-web">Web</li>
                </ul>
              </div>
            </div>
    
            <div class="row portfolio-container" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-duration="500">
    
              <div   class="col-lg-4 col-md-6 portfolio-wrap">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-1.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-web">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-2.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-app">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-3.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-card">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-4.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-web">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-5.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-app">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-6.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-card">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-7.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-card">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-8.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
              <div class="col-lg-4 col-md-6 portfolio-wrap filter-web">
                <div class="portfolio-ite">
                  <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-9.jpg') }}" class="img-fluid" alt="">
                  <div class="portfolio-info">
                    <div style="right: auto; float: right; z-index: 9999000" class="text-white mb-1" >
                        <button data-bs-toggle="modal" data-bs-target="#modelNoticia" style="border: none; background: none; color: white; font-size: 1.5pc;" ><i class="bx bx-link"></i></button>
                    </div>
                    <div class="mt-4">
                        <h3>Web 3</h3>
                        <div class="m-2">
                            <p style="color: white; text-align: justify;">Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
    
                        </div>  
                    </div>
                  </div>
                </div>
              </div>
    
            </div>
    
          </div>
        </section><!-- End Portfolio Section -->
    </div>
    
    
    
    <!-- Modal  de notiocia -->
    <div class="modal fade" id="modelNoticia" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div style="right: auto; float: right; z-index: 9999000" class="m-3 text-white">
            <button style="color: white;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>  
        <div class="modal-dialog modal-xl">
         <div class="modal-content">
          
          <div class="modal-body">
                <!-- ======= Our Portfolio Section ======= -->
                <section class="breadcrumbs">
                <div class="container">
    
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Portfolio Details</h2>
                   
                    </div>
    
                </div>
                </section><!-- End Our Portfolio Section -->
    
                <!-- ======= Portfolio Details Section ======= -->
                <section id="portfolio-details" class="portfolio-details">
                <div class="container">
    
                    <div class="row gy-4">
    
                    <div class="col-lg-8">
                        <div class="portfolio-details-slider swiper">
                        <div class="swiper-wrapper align-items-center">
    
                            <div class="swiper-slide">
                                <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-1.jpg') }}" alt="">
                            </div>
    
                            <div class="swiper-slide">
                                <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-2.jpg') }}" alt="">
                            </div>
    
                            <div class="swiper-slide">
                                <img src="{{ asset('css/web_site_style/assets/img/portfolio/portfolio-3.jpg') }}" alt="">
                            </div>
    
                        </div>
                        <div class="swiper-pagination"></div>
                        </div>
                    </div>
    
                    <div class="col-lg-4">
                        <div class="portfolio-info">
                        <h3>Project information</h3>
                        <ul>
                            <li><strong>Category</strong>: Web design</li>
                            <li><strong>Client</strong>: ASU Company</li>
                            <li><strong>Project date</strong>: 01 March, 2020</li>
                            <li><strong>Project URL</strong>: <a href="#">www.example.com</a></li>
                        </ul>
                        </div>
                        <div class="portfolio-description">
                        <h2>This is an example of portfolio detail</h2>
                        <p>
                            Autem ipsum nam porro corporis rerum. Quis eos dolorem eos itaque inventore commodi labore quia quia. Exercitationem repudiandae officiis neque suscipit non officia eaque itaque enim. Voluptatem officia accusantium nesciunt est omnis tempora consectetur dignissimos. Sequi nulla at esse enim cum deserunt eius.
                        </p>
                        </div>
                    </div>
    
                    </div>
    
                </div>
                </section><!-- End Portfolio Details Section -->
          </div>
        </div>
      </div>
    </div>


@endsection

@section('scripts')
    @parent

    <script>
        var seletor=$("#noticia");
        var posicao=$(seletor).offset().top;
        $("html, body").animate({scrollTop: posicao - 98})
    </script>

@endsection