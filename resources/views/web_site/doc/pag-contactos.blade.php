@section('title',__('CONTACTOS'))
@extends('web_site.website_layout')


@section('content')

    <div id="contactos">
        <section class="breadcrumbs">
            <div class="container">
    
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Contactos</h2>
                </div>
    
            </div>
            </section><!-- End Contact Section -->
    
            <!-- ======= Contact Section ======= -->
            <section class="contact" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-duration="500">
            <div class="container">
    
                <div class="row">
    
                <div class="col-lg-6">
    
                    <div class="row">
                    <div class="col-md-12">
                        <div class="info-box">
                            <i class="bx bx-map"></i>
                            <h3>Endereço</h3>
                            <p>
                                Angola, Benguela <br>
                                Rua Manuel Joaquim Filipe, nº 144<br><br>
                                <!-- United States <br><br> -->
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                        <i class="bx bx-envelope"></i>
                        <h3>E-mail</h3>
                        <p>ispm.direccao@hotmail.com<br>ispm.direccao@hotmail.com</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                        <i class="bx bx-phone-call"></i>
                        <h3>Telefone</h3>
                        <p>272 201 610<br>272 201 610</p>
                        </div>
                    </div>
                    </div>
    
                </div>
    
                <div class="col-lg-6">
                    <form action="forms/contact.php" method="post" role="form" class="php-email-form">
                    <div class="row">
                        <div class="col-md-6 form-group">
                        <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6 form-group mt-3 mt-md-0">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group mt-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                    </div>
                    <div class="my-3">
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">Your message has been sent. Thank you!</div>
                    </div>
                    <div class="text-center"><button type="submit">Send Message</button></div>
                    </form>
                </div>
    
                </div>
    
            </div>
            </section><!-- End Contact Section -->
    
            <!-- ======= Map Section ======= -->
            <section class="map mt-2">
            <div class="container-fluid p-0">
                <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2sbg!4v1579767901424!5m2!1sen!2sbg" frameborder="0" style="border:0;" allowfullscreen=""></iframe> -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15575.90654778237!2d13.4085599!3d-12.5837914!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x7b4644daa1c4d69e!2sInstituto%20Superior%20Universit%C3%A1rio%20Maravilha!5e0!3m2!1spt-PT!2sao!4v1651053042602!5m2!1spt-PT!2sao" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            </section><!-- End Map Section -->
    </div>

@endsection

@section('scripts')
    @parent
    
    <script>
        var seletor=$("#contactos");
        var posicao=$(seletor).offset().top;
        $("html, body").animate({scrollTop: posicao - 98})
    </script>

@endsection