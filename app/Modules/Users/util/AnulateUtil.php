<?php

namespace App\Modules\Users\util;

use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Enum\CodevNamedEnum;
use App\Modules\GA\Models\LectiveYear;
use Carbon\Carbon;
use DB;

class AnulateUtil{
    
    public function anulatePropinaArticleRequest($lective_year, $user_id){
        
        $currentData = Carbon::now();

        $lectiveYear = LectiveYear::whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();

        $emoluments = EmolumentCodevLective(CodevNamedEnum::PROPINA,$lective_year)->map(function($q){
            return $q->id_emolumento;
        });
        
        $articleRequest = ArticleRequest::whereIn('article_id', $emoluments)
            ->where('user_id', $user_id)
            ->where('status','!=','total')
            ->orderBy('month', 'ASC')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by');

        if(isset($lectiveYear->id) && $lectiveYear->id == $lective_year){
            $month = $currentData->format('m');
            $articleRequest = $articleRequest->where('month', '>=', $month);
        }

        $articleRequest = $articleRequest->get();

        $tam = sizeof($articleRequest);
        if($tam == 0) return;
        
        switch($articleRequest[0]->status){
            case "partial":
                $articleRequest->shift();
                $this->deletedArticleRequest($articleRequest);
                break;
            case "pending":
                $this->deletedArticleRequest($articleRequest);
                break;
        }
    }
         
    public function deletedArticleRequest($articleRequest){
        foreach($articleRequest as $article){
            if($article->status == "pending"){
                ArticleRequest::where(["id" => $article->id])
                ->update(["deleted_at" => now(), "deleted_by" => auth()->user()->id ]);
            }
        }
    }

}