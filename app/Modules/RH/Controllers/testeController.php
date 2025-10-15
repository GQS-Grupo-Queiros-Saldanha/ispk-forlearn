<?php

namespace App\Modules\RH\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;


use Cache;
use App\Helpers\LanguageHelper;
use App\Modules\Cms\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Role;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleType;

use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;
use App\Model\Institution;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use Illuminate\Support\Facades\Storage;

class testeController extends Controller
{



    //Pegar dados singular do estudante
    public function index(Request $request){

        $emolumentoEsP=['gee','coa','eco'];
        $emolumento_Especialidade  = EmolumentCodeV($emolumentoEsP[1],9);

        if($emolumento_Especialidade->isEmpty()){
            // Toastr::warning(__('A forLEARN não encontrou um emolumento da especialidade [ COA configurado no ano lectivo selecionado].'), __('toastr.warning'));
            // return redirect()->route('matriculations.index');
            return "forLAa";
        }

        $articlePropinaId = $emolumento_Especialidade[0]->id_emolumento;

        return $emolumento_Especialidade;

       return response()->json(["ola"]);
    }



    //Pegar dados singular do estudante
    public function matriculations_student(Request $request){

          
         $currentData = Carbon::now();

         $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();

         $idUser=base64_decode($request->id);

          $Matriculas= DB::table('matriculations as mt')
          ->where('user_id',$idUser)
          ->whereNull('mt.deleted_at')
          ->join('lective_years as al','al.id','mt.lective_year')
          ->leftJoin('lective_year_translations as ltY', function ($join) {
            $join->on('ltY.lective_years_id', '=', 'al.id');
            $join->on('ltY.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ltY.active', '=', DB::raw(true));
          })
          ->select(['mt.id as idMat','mt.code','mt.course_year','mt.lective_year','ltY.display_name'])
          ->get();
      
           $lectiveYears = $Matriculas->pluck('lective_year')->all();

           $extra = $this->emolumento($idUser, $lectiveYears )->get();

         
          $Emolumentos=collect($extra)->map(function($item){
                 $mes = [1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"];
                 $item->codev==2? $item->emolumento="Propina ".$mes[$item->mes]."(".$item->year.")":$item;
                 $item->codev==4? $item->emolumento=$item->emolumento."(".$item->disciplina."-[".$item->codigo."])":$item;
                 is_int($item->valor_base)? $teste = number_format($item->valor_base, 2, ".", ",") : $item->valor_base;

                
                 return $item;
          });
         
          $Other = false;
          foreach ($Emolumentos as $emolumento) {
                if ($emolumento->lectiveYear == $lectiveYearSelected->id) {
                    $Other = true;
                    break;
                }
            }

          $dados = [
            'Matriculas' => $Matriculas,
            'Emolumentos' => $Emolumentos,
            'Ano_actual' => $lectiveYearSelected->id,
            'Saldo' => $lectiveYearSelected->id,
            'countEmolumentos' => count($Emolumentos),
            'Outro' => $Other,
        ];

       return response()->json($dados);
    }


    //Pegar Emolumento 
    
    private function emolumento($id, $idLectiveS)
    {

        $emolumento = DB::table('article_requests as artR')
            ->join('articles  as art', 'art.id', 'artR.article_id')
            ->leftJoin('article_translations as ct', function ($join) {
                $join->on('ct.article_id', '=', 'art.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as disc', 'disc.id', 'artR.discipline_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disc.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select('ct.display_name as emolumento', 'art.id_code_dev as codev', 'art.anoLectivo as lectiveYear', 'artR.id as id_artiRequest', 'artR.base_value as valor_base', 'artR.month as mes', 'artR.status', 'artR.year', 'artR.discipline_id', 'dt.display_name as disciplina', 'disc.code as codigo')
            ->where('artR.user_id', $id)
            ->whereNull('artR.deleted_by')
            ->whereNull('artR.deleted_at')
            ->whereIn('art.anoLectivo',$idLectiveS);
            // ->distinct();
            


        return $emolumento;
    }



}
