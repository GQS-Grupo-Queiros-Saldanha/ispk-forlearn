<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rotina</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <button style="width: 30px; border-radius:600px; padding:30px; font-size:16pt;" class="fa fa-search"></button>
<Center>
    <h1>Formulário para controlo de rotina dos emulumentos</h1>
    <form action="{{route('emulumento_update')}}" target="_blank">
        @csrf
        @method('post')
        {{-- {{$cursos}} --}}
        <label for="">Curso</label><br>
        <select name="id_curso" id="" style="width: 450px; padding:20px; font-size:25pt;" >
            @foreach ($cursos as $item)
            <option value="{{$item->id}}"> {{$item->display_name}}</option> 
            @endforeach
        </select>
        
        <br>
        <br>
        <label for="">Id do Emulumento antigo [Propina]</label><br>
        
      
        <select name="emulumento_antigo" id="" style="width: 850px; padding:20px; font-size:25pt;" >
            @foreach ($old_emu as $item)
            @if ($item['id']!=null)
            <option value="{{$item->id}}"> {{$item->display_name}} ({{$item->base_value}})</option>   
            @endif
            @endforeach
        </select>
        <br>
        <br>
        <label for="">Id do Emulumento Novo [Propina]</label><br>
        <select name="emulumento_novo" id="" style="width: 850px; padding:20px; font-size:25pt;" >
            @foreach ($New_emu as $item)
            @if ($item['id']!=null)
            <option value="{{$item->id.",".$item->base_value}}"> {{$item->display_name}} ({{$item->base_value}})</option> 
            @endif
            @endforeach
        </select>

       
        <br>
        <br>
    
        <br>
        <br>
        <button style="width: 400px; padding:20px; font-size:25pt;">Actualizar</button>
    </form>

<br>
<br>
<br>
<br>
<br>
<strong>Nota: Apenas nos alunos do primeiro ano e metriculados Pelos utilizadores(Moisés Saldanha, Marcos e Pedro) no dia 30 de dezembro de 2021.   actualizar com os valores certos nas respectivas tabelas.</strong>
</Center>

    
</body>
</html>