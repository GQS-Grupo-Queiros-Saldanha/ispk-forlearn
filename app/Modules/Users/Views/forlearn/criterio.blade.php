<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forlearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>

        body{height:100%;margin:0;background-image:url('https://media.istockphoto.com/id/2197595309/photo/man-with-book-in-library.webp?s=2048x2048&w=is&k=20&c=Ve62VE8VffAF0iUppT8_R62XbWkakQlareIqboiGzp4=');/*background-color:rgba(0,0,0,0.3);*/color:#fff;display:flex;align-items:center;justify-content:center;}
        form{border:none; background-color:rgba(0,0,0,0.1); border-radius:.75rem; padding:2rem; width:100%; max-width:400px;}
        input{background-color:rgba(0,0,0,0.1);}
        .conteudo{top:50%; margin:0 auto; padding:2rem; width:100%; max-width:400px;}
        h1{font-size:2rem; font-weight:700; text-align:center; margin-bottom:1rem; color: #3399cc;}
    </style>
</head>
<body>
    <div class="conteudo">
        <h1>forLEARN</h1>
        <h4>Este é uma actualização da forlearn. </h4>
        <p>porfavor atulize o seu Whatsapp pará poder utilizar os novos serviços</p>
        <form action="{{ route('actualizar-whatsapp') }}" method="POST" class="form-control" id="formularioLogin">
            <div class="mb-3 ">
                <div id="error" class="text-danger"></div>
                <label for="criterio" class="form-label">
                    <i class="bi bi-whatsapp"></i>
                    <strong>Whatsapp</strong>
                </label>
                <input type="text" class="form-control" id="numero" name="criterio" placeholder="ex: 945347861" required>
                <input type="hidden" name="id" value="{{ $id }}">
            </div>
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">Prosseguir</button>
                <a href="{{ url('/') }}" class="btn btn-secondary">Não tenho!</a>
            </div>
        </form>
    </div>
    <script>
      const inputNumero = document.getElementById('numero');
      const error = document.getElementById('error');
      const formulario = document.getElementById('formularioLogin')

      function validarNumero(numero){
        if(!/^\d{0,9}$/.test(numero)){
          return 'O numero deve conter apenas 9 digitos'
        }
        if(numero.length < 9){
          return ""; // ainda a digitar
        }
        if(numero[0] !== '9'){
          return 'O numero deve comecar com 9'
        }
        if(numero[1] === '9' && numero[2] === '9' ){
          return 'O numero 999 nao é permitido!'
        }
        if (numero.startsWith('9')) {
          const restantes = numero.slice(1); // pega os 8e últimos dígitos
          const primeiroRepetido = restantes[0];

          if ([...restantes].every(d => d === primeiroRepetido)) {
            return 'Números inventados não são permitido.';
          }
        }
         // Bloqueio de repetições tipo 990000000, 998888888
        if (numero.startsWith('99')) {
          const restantes = numero.slice(2); // pega os 7 últimos dígitos
          const primeiroRepetido = restantes[0];

          if ([...restantes].every(d => d === primeiroRepetido)) {
            return 'Números inventados (repetitivos) não são permitidos após 99.';
          }
        }
        return '' //tudo certo
      }

       inputNumero.addEventListener('input', function () {
        const numero = inputNumero.value.trim();
        const mensagemErro = validarNumero(numero);
        error.textContent = mensagemErro;
      });


      formulario.addEventListener('submit', function (event) {
      const numero = inputNumero.value.trim();
      const mensagemErro = validarNumero(numero);

      if (mensagemErro !== '') {
        error.textContent = mensagemErro;
        event.preventDefault();
      }
    });
      
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>