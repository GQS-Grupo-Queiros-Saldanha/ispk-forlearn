


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <title>ISPM-Criar usuário</title>

    <!-- <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   <link rel="stylesheet" href="{{ asset('css/style.css')}}">



    <!-- Links -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png')}}">
    <link rel="manifest" href="forlearn.ispm.co.ao/manifest.webmanifest">
    <link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">

    <style >

    .ld{
      font-weight: bold;
      color: #17a2b85e;
      cursor: pointer;
      font-family:30pt;
    }
    .ld:hover{
      transition: .5s;
      cursor: pointer;
      color: #1bb5cc;
    }
   
   .btn_registrar{
      background-color: #1bb5cc;
      color:white;
   }
   .btn_registrar: hover{
      background-color: red;
      color: white;
   }
           .user-profile-image {
        width: 200px !important;
        }

        input#full_name::placeholder{
            color: red;
        }
        input#id_number::placeholder{
            color: red;
        }
  </style>
</head>
<body>

    <div class="d-lg-flex half">

    <div class="bg order-1 order-md-2" style="background-image: url('{{asset('img/background_students.jpg')}}');"></div>

    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center" style="color: #1bb5cc;">
          <div class="col-md-7 ">
            <h2><img src="{{asset('img/logo.jpg')}}"></h2>

            <h1 style="color:#1bb5cc;"><strong >Criar perfil de candidato </strong></h1>

            <p class="mb-4">Para finalizar a sua pré-candidatura, coloque uma palavra-passe, que não deve esquecer, para acesso a plataforma <b title="">forLEARN <sup>® </sup></b>.</p>

            <form method="POST" action="{{ route('candidate.store') }}" accept-charset="UTF-8"
                enctype="multipart/form-data" target="_blank">
                @csrf

                <div hidden>
                    <input type="text" value="{{ $user->id }}" name="candidateId">
                </div>

              <div class="form-group first ">
                  <label for="name">Primeiro e o último nome</label>
                  <input type="text" class="form-control" placeholder="Aqui vem o primeiro e o último nome " required="" autocomplete="name" name="name" type="text" id="name" readonly value="{{ $name }}">
              </div>


              <div class="form-group first ">
                <label for="email">e-mail</label>
                <input  class="form-control" placeholder="Aqui vem o e-mail" readonly="" required autocomplete="email" name="email" type="email" id="email" value="{{ $email }}">
              </div>

            <div class="form-group first ">
                <label for="full_name">Nome completo</label>
                <input class="form-control" placeholder="Escreva o nome completo" required=""utocomplete="name" name="full_name" type="text" id="full_name"readonly value="{{ $user->name }}">
              </div>

            <div class="form-group last mb-3">
                <label for="id_number">Crie uma palavra-passe</label>
                <input class="form-control" placeholder="Palavra-passe com 8 digitos" required=""autocomplete="email" name="id_number" type="password" id="id_number" minlength="8">
              </div>



              <div class="d-flex mb-1 align-items-center">
                <label class="control control--checkbox mb-0">
                </label>
                <!-- <span class="ml-auto"><a href="canditatura.php" target="_blank" class="forgot-pass">Registrar-se</a></span> -->
              </div>




              <div class="card" hidden="">

                          <h5 class="card-title mb-3">Cargos</h5>
                          <select name="roles" id="">
                                  <option value="{{$roles->id}}" selected>
                                      {{$roles->currentTranslation->display_name}}
                                  </option>
                           </select>

               </div>
                <div id="nextBtn" hidden>
                  <i class="fas fa-plus"></i>
                     <input type="submit" value="Próximo" class="btn btn-block btn_registrar" >
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>


  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous">
  </script>



  <script>
      $(function () {
          var hasName = false, hasFullName = false, hasPassword = false;
          $("#name").on('blur', function() {
              var name = $(this).val();
              var result = name.split(" ");
              // alert(result.length);
                if (result.length === 2) {
                    hasName = true;
                  $("#name").removeClass('is-invalid');
                  $("#name").addClass('is-valid');

                  $.ajax({
                      url: "/candidaturas/email_convert/" + result,
                      type: "GET",
                      data: {
                          _token: '{{ csrf_token() }}'
                      },
                      cache: false,
                      dataType: 'json',

                      success: function (dataResult) {
                              $("#email").val(dataResult);
                      },
                      error: function (dataResult) {
                      // alert('error' + result);
                      }
                  });
               }else{
                   hasName = false;
                  $("#name").removeClass('is-valid');
                  $("#name").addClass('is-invalid');
                  $("#email").val("");
               }


               if (hasPassword) {
                      $("#nextBtn").prop('hidden', false);
                  }else{
                      $("#nextBtn").prop('hidden', true);
                  }
          });


          $("#id_number").on('blur', function(){
              var count = $(this).val().length;
              if(count === 8){
                  hasPassword = true;
                  $("#id_number").removeClass('is-invalid');
                  $("#id_number").addClass('is-valid');
              }else{
                  hasPassword = false;
                  $("#nextBtn").prop('hidden', true);
                  $("#id_number").addClass('is-invalid');
                  $("#id_number").removeClass('is-valid');
              }

              if (hasPassword) {
                      $("#nextBtn").prop('hidden', false);
                  }else{
                      $("#nextBtn").prop('hidden', true);
                  }
          });

          $("#full_name").on('blur', function(){
              var name = $(this).val();
              var result = name.split(" ");
              // alert(result.length);
                if (result.length >= 2) {
                    hasFullName = true;
                  $("#full_name").removeClass('is-invalid');
                  $("#full_name").addClass('is-valid');


               }else{
                   hasFullName = false;
                  $("#full_name").removeClass('is-valid');
                  $("#full_name").addClass('is-invalid');
               }

                if (hasPassword) {
                      $("#nextBtn").prop('hidden', false);
                  }else{
                      $("#nextBtn").prop('hidden', true);
                  }
          });
          $('input[name="email"]').on('blur', function () {
              Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}', '{{ $user->id ?? '' }}');
          });
      });
  </script>

</body>
</html>