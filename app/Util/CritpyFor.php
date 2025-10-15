<?php

namespace App\Util;

class CritpyFor{

    protected const TAM = 30;

    protected const WORDS = [
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','w','z',
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T','U','V','X','Y','W','Z',
        '1','2','3','4','5','6','7','8','9',
        '$','#','@','%'
    ];

    private static function generatorWord(): string{
        $tam = sizeof(static::WORDS);
        $index = rand(0,$tam -1);
        return static::WORDS[$index];
    }

    private static function reapeat(): string{
        $join = '';
        for($i = 0; $i < static::TAM ; $i++) $join .= static::generatorWord();
        return $join;
    }

    public static function encode(string $word): string{
        return static::reapeat().$word.static::reapeat();
    }

    public static function decode($word_encode){
        $word = substr($word_encode,static::TAM);
        $tam = strlen($word);
        $size = $tam - static::TAM;
        return substr($word,0, $size);
    }

}