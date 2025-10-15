<?php

use Illuminate\Support\Facades\DB;

function image_photo(){
    $image = DB::table("user_parameters")->where('parameters_id',25)->where('users_id',auth()->user()->id)->first();
    return $image->value ?? null;
}

function getcodeCategory($nome_category){

    return $getcodeCategory=DB::table('code_category_developer as category')
    ->leftJoin('code_developer as code_dev','code_dev.id_code_category','=','category.id')
    // ->where('category.code','=',$nome_category)
    ->select([
        'code_dev.id as id_code',
        'code_dev.code as code',
        'code_dev.name_code as nome_code'
    ])
    ->get();
}

function getArticles()
{
    try{
        return $getArticles=DB::table('articles as art')
        ->leftJoin('article_translations as at', function ($join) {
            $join->on('art.id', '=', 'at.article_id')
             ->where('at.language_id', '=',1)
             ->where('at.active', '=', 1);
        })
        ->leftJoin('code_developer as code_dev','code_dev.id','=','art.id_code_dev')
        ->select([
            'at.display_name as nome',
            'code_dev.code as nome_code',
            'art.id as id_artigo'
        ])
        ->whereNull('art.deleted_at')
        ->whereNull('at.deleted_at')
        ->get();     
    } catch (Exception | Throwable $e) {
        return response()->json(500);
        //  Log::error($e);
         return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
     }
}
function getImposto(Type $var = null)
{
    return $getImposto=DB::table('imposto as impost')
    ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev')
    ->select([
        'impost.display_name as nome',
        'code_dev.code as nome_code',
        'impost.discricao as descricao',
        'impost.id as id_artigo'
    ])
    ->whereNull('impost.deleted_by')
    ->whereNull('impost.deleted_at')
    ->get();
}

function getStates()
{
    try{
        return $getArticles=DB::table('states as state')
        ->leftJoin('code_developer as code_dev','code_dev.id','=','state.id_code_dev')
        ->select([
            'state.name as nome',
            'code_dev.code as nome_code',
            'state.id as id_artigo',
            'state.initials as initials'
        ])
        // ->whereNull('state.deleted_at')
        // ->whereNull('state.deleted_at')
        ->get()->map(function ($q)
        {
            $q->nome= $q->nome.' ('.$q->initials.')';
            return $q;
        });     
    } catch (Exception | Throwable $e) {
        return response()->json(500);
        //  Log::error($e);
         return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
     }
}

