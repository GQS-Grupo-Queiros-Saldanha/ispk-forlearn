<?php


# ATENÇÃO: Este arquivo foi criado com o intuito de resolver as questões relacionadas aos a pasta storage
# Convém não apagares o mesmo em caso de dúvidas porque isto vai causar uma paragem total na plataforma

$url = $_SERVER['REQUEST_URI']; 

if (strpos($url, 'storage') == true) {
    
    if (stripos($url, 'app/public/') == true) {
        
    } else {
        
        $url = 'https://' . $_SERVER['HTTP_HOST'] . '/'.str_replace('storage/', 'storage/app/public/', $url);
       
        header("Location: {$url}");exit();return 1;
        
    }
    
} 



?>