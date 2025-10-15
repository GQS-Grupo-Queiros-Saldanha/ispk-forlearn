<?php

namespace App\Modules\Users\util;

use App\Modules\Users\util\EnumVariable;
use App\Modules\Users\Models\User;
use DB;

class UserUtil{
    
    private $specialCharacters = [
            "á" => "a", "à" => "a", "â" => "a", "ã" => "a" ,"Á" => "A", "À" => "A", "Â" => "A", "Ã" => "A",
            "È" => "E", "É" => "E", "è" => "e", "é" => "e", "Ê" => "E", "ê" => "e",
            "Ì" => "I", "Í" => "I", "ì" => "i", "í" => "i", "Î" => "I", "î" => "i",
            "Ç" => "c", "ç" => "c",
            "ó" =>"o", "ò" =>"o", "Ó" => "O", "Ò" => "O", "Ô" => "O", "Õ" => "O", "õ" => "o", "ô" => "o",
            "Ù" => "U", "Ú" => "U", "ù" =>"u", "ú" =>"u", "û" => "u", "Û" => "U"
    ];
    
    private function convertToEmailNotLast($name, $prefix = ""){
        $islastEmail= false;
        $pieces = explode(",", $name);
        $lenght = strlen($pieces[0]);
        $nameLenght = count($pieces);
        $firstAndLastName = $pieces[0] ." ".  $pieces[$nameLenght - 1];
        $specialCharacters = $this->specialCharacters;
        $lastEmail= strtolower(strtr($pieces[0], $specialCharacters).".". strtr($pieces[$nameLenght - 1], $specialCharacters) . EnumVariable::$CONVERT_TO_EMAIL);
        for ($i=0; $i <= $lenght; $i++) {
                $letter = strtr($pieces[0], $specialCharacters);
                $lastNameWithoutSpecialCharacters = strtr($pieces[$nameLenght - 1], $specialCharacters);
                $email = $prefix . strtolower(substr($letter, 0, $i + 1) .".". $lastNameWithoutSpecialCharacters . EnumVariable::$CONVERT_TO_EMAIL);
                $checkEmail = User::where('users.email', '=', $email)->get();
                if ($checkEmail->isEmpty()) {
                    $email = $email;
                    break;
                }else if($lastEmail== $email){
                    $islastEmail = true;
                    break;
                }
        }
        return ['name' => $firstAndLastName, 'email' => $email, 'islastEmail' => $islastEmail ];
    }
    
    public function convertToEmail($name){
        $data = $this->convertToEmailNotLast($name);
        if(!$data['islastEmail']) return  response()->json($data);
        $specialCharacters = $this->specialCharacters;
        $pieces = explode(",", $name);
        $tam = sizeof($pieces);
        $lastNameInEmail = strtolower(strtr($pieces[$tam-1] . EnumVariable::$CONVERT_TO_EMAIL, $specialCharacters));
        $firstNameInEmailWithPoint = str_replace($lastNameInEmail,"",$data['email']);
        return $this->convertToEmailNotLast($name, $firstNameInEmailWithPoint);
    }    
    
}