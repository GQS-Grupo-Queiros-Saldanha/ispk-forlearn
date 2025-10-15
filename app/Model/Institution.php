<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    //
    protected $fillable = [
        'nome',
        'morada',
		'provincia',
		'municipio',
		'contribuinte',
		'capital_social',
		'registro_comercial_n',
		'registro_comercial_de',
		'dominio_internet',
		//CONTACTOS
		'telefone_geral',
		'telemovel_geral',
		'email',
		'whatsapp',
		'facebook',
		'instagram',
		//ACADÉMICAS
		'director_geral',
		'vice_director_academica',
		'vice_director_cientifica',
		'daac',
		'gabinete_termos',
		'secretaria_academica',
		//ADMINISTRATIVAS
		'director_executivo',
		'recursos_humanos',
		//PROPRIETARIO
		'nome_dono',
		'nif',
		//ARQUIVOS
		'logotipo',
        'decreto_instituicao',
		'instituicao_arquivo',
        'decreto_cursos',
		'cursos_arquivo',       
    ];
}
