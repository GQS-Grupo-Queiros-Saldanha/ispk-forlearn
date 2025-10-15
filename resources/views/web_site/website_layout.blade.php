<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ISUM</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('css/web_site_style/assets/img/logoISPM.png') }}" rel="icon">
  <link href="{{ asset('css/web_site_style/assets/img/logoISPM.png') }}" rel="apple-touch-icon">

  <!-- Vendor CSS Files -->
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/animate.css/animate.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/aos/aos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/boxicons/css/boxicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/glightbox/css/glightbox.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/vendor/swiper/swiper-bundle.min.css') }}">
  <script src="{{ asset('css/web_site_style/assets/js/jquery.js')}}"></script>
  
   <!-- Template Main CSS File -->
  <link rel="stylesheet" href="{{ asset('css/web_site_style/assets/css/style.css') }}">
  
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center header-transparent">
    <div class="container d-flex justify-content-between align-items-center">

      <div class="logo">
        <h1 class="text-light"> <a href="/web_site">
            <img src="{{ asset('css/web_site_style/assets/img/logoISPM.png')}}" alt="" class="img-fluid mr-2 mw-100"></a> <a href="/web_site"><span>ISUM</span></a> </h1>
        <!-- Uncomment below if you prefer to use an image logo -->       
      </div>

      <nav id="navbar" class="navbar">
        <ul>
          <li  ><a class="active " href="/web_site">Início</a></li>
          <li  ><a href="/website_sobre">Sobre nos</a></li>
          <li  class="dropdown"><a href="#"><span>Curso <i class="bi bi-chevron-down"></i></span> </a>
            <ul style="padding: 0;" class="pt-0">
              <div class="m-0 p-1 bg-white" ></div>
              <li style="padding: 0; margin: 0px;" class="dropdown"><a href="#"><span>Departamentos <i class="bi bi-chevron-right"></i></span> </i></a>
                <ul>
                    @foreach ($Department as $departamentos)
                        <li style="padding: 0; margin: 0px;"><a href="#">{{ $departamentos->departamento_nome }}</a></li>
                    @endforeach
                </ul>
              </li>
              <li style="padding: 0; margin: 0px;" class="dropdown"><a href="#"><span>Cursos <i class="bi bi-chevron-right"></i></span> </i></a>
                <ul>
                    @foreach ($Course as $cursos)
                        <li style="padding: 0; margin: 0px;"><a href="#">{{ $cursos->curso_nome }}</a></li>
                    @endforeach
                </ul>
              </li>
              <!-- <li><a href="#">Drop Down 2</a></li>
              <li><a href="#">Drop Down 3</a></li>
              <li><a href="#">Drop Down 4</a></li> -->
            </ul>
          </li>
          <li ><a href="/website_servico">Serviços</a></li>
          <li><a  href="/website_eventos">Eventos</a></li>
          <li ><a href="/website_noticia">Notícias</a></li>
          <li ><a href="/website_contactos">Entre em contacto</a></li>
          <li><a target="_blank" href="https://www.forlearn.ao/pt/login">Portal </a> <i></i></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header>

  <!-- ======= sessão welcome ======= -->
  <section id="hero" class="d-flex justify-cntent-center align-items-center">
    <div id="heroCarousel" class="container carousel carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

      <!-- Slide 1 -->
      <div class="carousel-item active">
        <div class="carousel-container">
          <h2 class="animate__animated animate__fadeInDown">Bem-Vindo ao <span>Instituto Superior Universitário Maravilha</span></h2>
          <p class="animate__animated animate__fadeInUp">O I.S.U. Maravilha de Benguela (I.S.P.M), é uma Instituição de Ensino Superior Privado, integrada no subsistema de Ensino Superior.</p>
          <!-- <a href="" class="btn-get-started animate__animated animate__fadeInUp">Saber +</a> -->
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <div class="carousel-container">
          <h2 class="animate__animated animate__fadeInDown">Bem-Vindo ao <span>Instituto Superior Universitário Maravilha</span></h2>
          <p class="animate__animated animate__fadeInUp">O I.S.U. Maravilha de Benguela (I.S.P.M), é uma Instituição de Ensino Superior Privado, integrada no subsistema de Ensino Superior.</p>
          <!-- <a href="" class="btn-get-started animate__animated animate__fadeInUp">Saber +</a> -->
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <div class="carousel-container">
          <h2 class="animate__animated animate__fadeInDown">Bem-Vindo ao <span>Instituto Superior Universitário Maravilha</span></h2>
          <p class="animate__animated animate__fadeInUp">O I.S.U. Maravilha de Benguela (I.S.P.M), é uma Instituição de Ensino Superior Privado, integrada no subsistema de Ensino Superior.</p>
          <!-- <a href="" class="btn-get-started animate__animated animate__fadeInUp">Saber +</a> -->
        </div>
      </div>

      <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bx bx-chevron-left" aria-hidden="true"></span>
      </a>

      <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon bx bx-chevron-right" aria-hidden="true"></span>
      </a>

    </div>
  </section><!-- End Hero -->

  <!-- ======= corpo da site ======= -->
  <main id="main">
      
    <!-- ======= About Us Section ======= -->
    <section class="breadcrumbs">
      <div class="container">
    
        <div class="d-flex justify-content-between align-items-center">
          <h2>Acesso a forLEARN</h2>
          <!-- <ol>
            <li><a href="index.php">Home</a></li>
            <li>Sobre nós</li>
          </ol> -->
        </div>
    
      </div>
    </section><!-- End About Us Section -->
    
    <!-- if (href="/web_site") -->
    
    <!-- ======= About Section ======= -->
    <section class="about" data-aos="fade-up">
      <div class="container">
    
        <div class="row">
          <div class="col-lg-6">
            <iframe width="650" height="370" src="https://www.youtube.com/embed/eFzPu7oBz68" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
          <div class="col-lg-6 pt-4 pt-lg-0">
            <h3><i>Passos necessários para aceder à forLEARN.</i></h3>
            <p class="fst-italic">
                O vídeo a esquerda ensina todos os utilizadores do Instituto Superior Universitário Maravilha, quer sejam estudantes, professores ou funcionários, como aceder à plataforma forLEARN.
            </p>
            <ul>
              <li><i class="bi bi-check2-circle"></i> <b>1 Passo</b> - Aceder ao domino <b>ispm.co.ao</b></li>
              <li><i class="bi bi-check2-circle"></i> <b>2 Passo</b> - No canto superior à direita clicar em <b>Portal</b>.</li>
              <li><i class="bi bi-check2-circle"></i> <b>3 Passo</b> - Inserir as credenciais </li>
            </ul>
            <p>
              Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
              velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
              culpa qui officia deserunt mollit anim id est laborum
            </p>
          </div>
        </div>
    
      </div>
    </section>
    <!-- End About Section -->
    
    <!-- endif -->

    @section('content')
    @show

  </main><!-- End #main -->
  



  <!-- ======= area de contactos ======= -->
 
    <footer   id="footer" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-duration="500">

        <!-- <div class="footer-newsletter">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <h4>Our Newsletter</h4>
                <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
              </div>
              <div class="col-lg-6">
                <form action="" method="post">
                  <input type="email" name="email"><input type="submit" value="Subscribe">
                </form>
              </div>
            </div>
          </div>
        </div> -->

        <div class="footer-top">
          <div class="container">
            <div class="row">

              <!-- <div class="col-lg-3 col-md-6 footer-links">
                <h4>Useful Links</h4>
                <ul>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Home</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">About us</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Services</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Terms of service</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Privacy policy</a></li>
                </ul>
              </div> -->

              <!-- <div class="col-lg-3 col-md-6 footer-links">
                <h4>Our Services</h4>
                <ul>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
                  <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
                </ul>
              </div> -->

              <div class="col-lg-3 col-md-6 footer-contact">
                <h4>Contacte-nos</h4>
                <p>
                  Angola, Benguela <br>
                  Rua Manuel Joaquim Filipe, nº 144<br><br>
                  <!-- United States <br><br> -->
                  <strong>Telefone:</strong> 272 201 610<br>
                  <strong>E-mail:</strong> ispm.direccao@hotmail.com<br>
                </p>

              </div>

              <div class="col-lg-3 col-md-6 footer-info">
                <h3>Acerca do I.S.U.M</h3>
                <p>O I.S.U.M é uma Instituição de Ensino Superior Privado, integrada no subsistema de Ensino Superior.</p>
                <div class="social-links mt-3">
                  <!-- <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a> -->
                  <a href="https://www.facebook.com/Instituto-Superior-Universit%C3%A1rio-Maravilha-1426329907605447" class="facebook"><i class="bx bxl-facebook"></i></a>
                  <!-- <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a> -->
                  <!-- <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a> -->
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="container">
          <div class="copyright">
            &copy; Copyright <strong><span>forLEARN</span></strong>.Todos os direitos reservado
          </div>
          <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/free-bootstrap-template-corporate-moderna/ -->
            <a target="_blank" href="https://www.forlearn.ao">forLEARN</a>
          </div>
        </div>
    </footer><!-- End Footer -->
  

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
   <script src="{{ asset('css/web_site_style/assets/vendor/purecounte.r/purecounter.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/aos/aos.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/waypoints/noframework.waypoints.js')}}"></script>
  <script src="{{ asset('css/web_site_style/assets/vendor/php-email-form/validate.js')}}"></script>
  
  <!-- Template Main JS File -->
  <script src="{{ asset('css/web_site_style/assets/js/main.js')}}"></script>
    
  

</body>
<script>
  $(document).ready(function(){
  
   
  })
</script>
</html>

