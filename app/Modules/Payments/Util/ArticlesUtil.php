<?php

namespace App\Modules\Payments\Util;

use App\Modules\Users\Models\Matriculation;
use App\Modules\Payments\Models\Article;
use App\Modules\GA\Models\LectiveYear;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use Exception;
use Toastr;
use DB;

class ArticlesUtil
{
    private function warning($message)
    {
        Toastr::warning(__($message), __('toastr.warning'));
        return false;
    }

    public function validRequest($request)
    {
        if (!isset($request->ano_curricular)) {
            return $this->warning('Informa o ano curricular');
        }
        if (!isset($request->schedule_type)) {
            return $this->warning('Informa os ano lectivos');
        }
        if (!isset($request->emolument)) {
            return $this->warning('Informa os emolumentos');
        }
        if (!isset($request->month)) {
            return $this->warning('Informa os meses');
        }
        return true;
    }

    public function getArticleRules($id_anolectivo = null, $bolseiro = false)
    {
        $data = ['art_rule.id as id_ruleArtc', 'valor', 'art_rule.estado as estado', 'mes', 'ano_curricular', 'art_rule.created_at as created_at', 'at.display_name as display_name',
         'st.display_name as periodo_name','art_rule.id_articles','at.acronym','st.schedule_type_id'];

        $items = DB::table('artcles_rules as art_rule')
            ->leftJoin('articles as art', 'art.id', '=', 'art_rule.id_articles')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'art.id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('schedule_type_translations as st', function ($join) {
                $join->on('st.schedule_type_id', '=', 'art_rule.schedule_type_id');
                $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('st.active', '=', DB::raw(true));
            })
            ->whereNull('art_rule.deleted_by');

        if (isset($id_anolectivo)) {
            $items = $items->where('art_rule.ano_lectivo', '=', $id_anolectivo);
        }

        if ($bolseiro) {
            array_push($data, 'se.company');
            $items = $items->join('scholarship_entity as se', 'se.id', 'art_rule.scholarship_entity_id')->whereNotNull('art_rule.scholarship_entity_id')->whereNull('se.deleted_at');
        }

        if (!$bolseiro) {
            $items = $items->whereNull('art_rule.scholarship_entity_id');
        }
        $items = $items->select($data)->distinct();
        return $items->get();
    }

    public function getMatriculationClass($anoLectivo, $userId)
    {
        $matriculation = Matriculation::with('classes')
            ->where([
                'lective_year' => $anoLectivo,
                'user_id' => $userId,
                'deleted_at' => null,
            ])
            ->first();
        
       $classe = isset($matriculation->classes) ? collect($matriculation->classes)->filter( function($value, $key) use ($matriculation){
         return $value->year == $matriculation->course_year;
     }) : null;
     
     $classe = isset($classe) ? $classe->first() : null;
     
     
        

        $periodo = null;
        $ano_curricular = null;
        if (isset($classe->schedule_type_id)) {
            $periodo = $classe->schedule_type_id;
            $ano_curricular = $classe->year;
        }

        return (object) [
            'periodo' => $periodo,
            'ano_curricular' => $ano_curricular,
            'check' => isset($periodo) && isset($ano_curricular),
        ];
    }

    public function getRegraImplementada($anoLectivo, $userId, $article_request_id = null, $mes = [], $consultar = true)
    {
        $periodo = null;
        $ano_curricular = null;

        if ($consultar) {
            $getMatriculation = $this->getMatriculationClass($anoLectivo, $userId);
            $ano_curricular = $getMatriculation->ano_curricular;
            $periodo = $getMatriculation->periodo;
        }
        
       

        $getRegraImplementada = DB::table('artcles_rules as art_rule')
            ->join('articles as art', 'art.id', '=', 'art_rule.id_articles')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'art.id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->where('art_rule.ano_lectivo', '=', $anoLectivo)
            ->where('art_rule.id_articles', '=', null)
            ->where('art_rule.deleted_by', '=', null)
            ->select(['art_rule.id as id_ruleArtc', 'art_rule.valor as valor', 'art_rule.mes as mes', 'art_rule.ano_lectivo as ano_lectivo', 'art_rule.created_at as created_at', 'at.display_name as display_name']);

           
        $articlesBolseiros = $this->verifyIsBolseiro($userId);
         
        if(count($articlesBolseiros) > 0)
            return $getRegraImplementada->whereIn('art_rule.id', $articlesBolseiros)->get();
        
        return $this->getRegraArticle($getRegraImplementada, $ano_curricular, $periodo, $article_request_id, $mes);
    }

    public function getRegraImplementEmolu($anoLectivo, $userId, $article_request_id = null, $mes = [], $consultar = true)
    {
        $periodo = null;
        $ano_curricular = null;

        if ($consultar) {
            $getMatriculation = $this->getMatriculationClass($anoLectivo, $userId);
            $ano_curricular = $getMatriculation->ano_curricular;
            $periodo = $getMatriculation->periodo;
        }

        $getRegraImplementEmolu = DB::table('artcles_rules as art_rule')
            ->join('articles as art', 'art.id', '=', 'art_rule.id_articles')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'art.id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->join('article_requests as art_req', function ($join) {
                $join->on('art_req.article_id', '=', 'art_rule.id_articles');
                $join->on('art_req.month', '=', 'art_rule.mes');
            })
            ->where('art_req.user_id', $userId)
            ->where('art_rule.id_articles', '!=', null)
            ->where('art_rule.deleted_by', '=', null)
            ->where('art_rule.deleted_at', '=', null)
            ->whereNull('art_req.deleted_at')
            ->where('art_rule.ano_lectivo', '=', $anoLectivo)
            ->select(['art_req.id as id_art_req', 'art_rule.id_articles as id_articles', 'art_rule.id as id_ruleArtc', 'art_rule.valor as valor', 'art_rule.mes as mes', 'art_rule.ano_lectivo as ano_lectivo', 'art_rule.created_at as created_at', 'at.display_name as display_name']);

        $articlesBolseiros = $this->verifyIsBolseiro($userId);

        
        if(count($articlesBolseiros) > 0){
            $regraImplementEmoluBolseiro =   $this->getRegraImplementEmoluBolseiro($articlesBolseiros, $userId);
            
            return $regraImplementEmoluBolseiro;
           
        }
           
        return $this->getRegraArticle($getRegraImplementEmolu, $ano_curricular, $periodo, $article_request_id, $mes);
    }

    public function getRegraArticle($getRegra, $ano_curricular, $periodo, $article_request_id = null, $mes = [])
    {
        if (isset($article_request_id)) $getRegra = $getRegra->whereIn('art_req.id', $article_request_id);
        if ($ano_curricular) $getRegra = $getRegra->where('art_rule.ano_curricular', $ano_curricular);
        if ($periodo) $getRegra = $getRegra->where('art_rule.schedule_type_id', $periodo);
        if (sizeof($mes) > 0)  $getRegra = $getRegra->whereIn('art_rule.mes', $mes);
        return $getRegra->get();
    }

    public function verifyIsBolseiro($userId)
    {
        $items = DB::table('scholarship_holder as sh')
            ->join('scholarship_entity as se', 'se.id', 'sh.scholarship_entity_id')
            ->join('artcles_rules as ar', 'ar.scholarship_entity_id', 'sh.scholarship_entity_id')
            ->where('sh.user_id', $userId)
            ->where('se.type', 'PROTOCOLO')
            ->whereNotNull('ar.scholarship_entity_id')
            ->where('sh.are_scholarship_holder', 1)
            ->whereNotNull('ar.id_articles')
            ->whereNull('se.deleted_at')
            ->whereNull('ar.deleted_at')
            ->select(['ar.id as article_rule_id','sh.scholarship_entity_id','se.company','ar.id_articles'])
            ->get();
        
        return $items->map(function ($q) {
            return $q->article_rule_id;
        })->all();
    }

    public function getRegraImplementEmoluBolseiro($articlesBolseiros, $userId){
       
        return DB::table('artcles_rules as art_rule')
        ->whereNull('art_rule.deleted_at')
        ->whereNull('art_rule.deleted_by')
        ->whereNotNull('art_rule.scholarship_entity_id')
        ->join('articles as art', 'art.id', '=', 'art_rule.id_articles')
        ->leftJoin('article_translations as at', function ($join) {
            $join->on('at.article_id', '=', 'art.id');
            $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('at.active', '=', DB::raw(true));
        })
        ->join('article_requests as art_req', function ($join) use ($userId) {
            $join->on('art_req.article_id', '=', 'art_rule.id_articles')
            ->where('art_req.user_id', $userId);
            // ->where('art_req.month', 10);
        })
        ->whereIn('art_rule.id', $articlesBolseiros)
        //->where('articles.status','<>','partial')
        ->whereNull('art_req.deleted_at')
        ->whereNull('art_req.deleted_by')
        ->select([
            'art_req.id as id_art_req', 
            'art_rule.id_articles as id_articles', 
            'art_rule.id as id_ruleArtc', 
            'art_rule.valor as valor', 
            'art_req.month as mes', 
            'art_rule.ano_lectivo as ano_lectivo', 
            'art_rule.created_at as created_at', 
            'at.display_name as display_name'
        ])->get();        
    }

    public function getArticleByLectiveYear($lective_year = null)
    {
        $lectiveYear = !isset($lective_year) ? currentLectiveYear() : LectiveYear::find($lective_year);

        return Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'articles.id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('article_category as ac', 'ac.id', '=', 'articles.id_category')
            ->select(['articles.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'at.display_name', 'at.acronym', 'ac.name as category_name'])
            ->whereNull('articles.deleted_by')
            ->whereNull('articles.deleted_at')
            ->whereBetween('articles.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
            ->get();
    }

    public function getRegraValue($articleRequestsOrganizado, $getRegraImplementada, $getRegraImplementEmolu)
    {
        $status = false;
        if (count($getRegraImplementada) > 0) {
            foreach ($articleRequestsOrganizado as $key => $item) {
                foreach ($getRegraImplementada as $chave => $valor) {
                    if ($item->month == $valor->mes && $item->year != null && $item->discipline_id == '') {
                        $item->balance = -$valor->valor + $item->base_value + $item->balance;
                        $item->base_value = $valor->valor;
                        $status = true;
                    }
                }
            }
        } elseif (count($getRegraImplementEmolu) > 0 && count($getRegraImplementada) < 1) {
            
            foreach ($articleRequestsOrganizado as $key => $item) {
                foreach ($getRegraImplementEmolu as $chave => $valor) {
                   
                    if ($item->month == $valor->mes && $item->year != null && $item->discipline_id == '') {
                        $item->balance = -$valor->valor + $item->base_value + $item->balance;
                        $item->base_value = $valor->valor;
                        $status = true;
                    }
                }
            }
        }
        
        return (object)[ "request" => $articleRequestsOrganizado, "status" => $status];
    }

}
