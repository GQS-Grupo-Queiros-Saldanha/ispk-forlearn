<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Requests\ClassRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
// use Request;
use Throwable;
use Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\Institution;
use PDF;
class ClassesController extends Controller
{

    public function index()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                        ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            return view('GA::classes.index', compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
         
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function Duplicar_Turma(Request $request){

        try {
            //Pegar a turma 
            $consultar=DB::table('classes')
            ->whereId($request->flag_classes)
            ->first();

            //verifica existencia 
            $classe_exist=DB::table('classes')
            ->where('code', $request->code)
            ->where('lective_year_id',$request->lective_years)
            ->first();
            
            if($classe_exist){                            
    
                Toastr::warning(__('Não foi possível duplicar a turma porque detectou-se que já existe dados desta turma no ano lectivo selecionado para duplicação.'), __('toastr.warning'));
                return redirect()->route('classes.index');

            }
                  
            if($consultar){
                    //verifica existencia 
                    $consultar_exist=DB::table('classes')
                    ->whereId($request->flag_classes)
                    ->where('lective_year_id',$request->lective_years)
                    ->first();
                    
                    if(!$consultar_exist){
                     
                        DB::beginTransaction();

                        // Create a duplication
                        $class = new Classes([
                            'code' => $request->get('code'),
                            'display_name' => $request->get('display_name'),
                            'room_id' => $consultar->room_id,
                            'vacancies' => $consultar->vacancies,
                            'courses_id' =>$consultar->courses_id,
                            'year' => $consultar->year,
                            'schedule_type_id' => $consultar->schedule_type_id,
                            'lective_year_id' => $request->lective_years
                        ]);
            
                        $class->save();
            
                        DB::commit();

                        Toastr::success(__('Registo cadastrado com sucesso '), __('toastr.success'));
                        return redirect()->route('classes.index');
                    }
                
                    Toastr::warning(__('Não foi possível duplicar a turma porque detectou-se que já existe dados desta turma no ano lectivo selecionado para duplicação.'), __('toastr.warning'));
                    return redirect()->route('classes.index');

            }

    
            Toastr::error(__('Erro ao encontrar a turma que pretende-se duplicar, por favor tente novamente.'), __('toastr.error'));
            return redirect()->route('classes.index');
       }catch (Exception | Throwable $e) {
             return $e;
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }

    public function ajax()
    {
        try {
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();

            $model = Classes::join('users as u1', 'u1.id', '=', 'classes.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'classes.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'classes.deleted_by')
                ->leftJoin('room_translations as rt', function ($join) {
                    $join->on('rt.room_id', '=', 'classes.room_id');
                    $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('rt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'classes.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'classes.lective_year_id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->select([
                    'classes.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'rt.display_name as room',
                    'ct.display_name as course',
                    'lyt.display_name as lective_year'
                ])
            ->where('classes.lective_year_id', $lectiveYearSelected->id ?? 6);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::classes.datatables.actions')->with('item', $item);
                })
              /*  ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {

            $data = [
                'action' => 'create'
            ];

            return view('GA::classes.classes')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClassRequest $request
     * @return Response
     */
    public function store(ClassRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $class = new Classes([
                'code' => $request->get('code'),
                'display_name' => $request->get('display_name'),
                'room_id' => $request->get('room'),
                'vacancies' => $request->get('vacancies'),
                'courses_id' => $request->get('course'),
                'year' => $request->get('year'),
                'schedule_type_id' => $request->get('schedule_type'),
                'lective_year_id' => $request->get('lective_year')
            ]);

            $class->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::classes.store_success_message'), __('toastr.success'));
            return redirect()->route('classes.index');

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {

            // Find
            $class = Classes::whereId($id)
                ->with('room', 'course', 'scheduleType')
                ->firstOrFail();

            // Set relation keys
            $data = [
                'action' => $action,
                'class' => $class,
            ];

            return view('GA::classes.classes')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClassRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ClassRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find
            $class = Classes::whereId($id)->firstOrFail();

            // Update
            $class->code = $request->get('code');
            $class->display_name = $request->get('display_name');
            $class->room_id = $request->get('room');
            $class->vacancies = $request->get('vacancies');
            $class->courses_id = $request->get('course');
            $class->year = $request->get('year');
            $class->schedule_type_id = $request->get('schedule_type');
            $class->lective_year_id = $request->get('lective_year');

            $class->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::classes.update_success_message'), __('toastr.success'));
            return redirect()->route('classes.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $class = Classes::whereId($id)->firstOrFail();
            $class->delete();
            $class->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::classes.destroy_success_message'), __('toastr.success'));
            return redirect()->route('classes.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function classesBy($lectiveYearId)
    {
        try {

            $model = Classes::join('users as u1', 'u1.id', '=', 'classes.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'classes.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'classes.deleted_by')
                ->leftJoin('room_translations as rt', function ($join) {
                    $join->on('rt.room_id', '=', 'classes.room_id');
                    $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('rt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'classes.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'classes.lective_year_id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->select([
                    'classes.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'rt.display_name as room',
                    'ct.display_name as course',
                    'lyt.display_name as lective_year'
                ])
            ->where('classes.lective_year_id', $lectiveYearId);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::classes.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    
      public function gerarPDF($lectiveYearId = null)
  {
    try {
      // Obtém a data atual
      $currentData = Carbon::now();

      // Se nenhum ano letivo for fornecido, seleciona o ano letivo atual
      if (is_null($lectiveYearId)) {
        $lectiveYearSelected = DB::table('lective_years')
          ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
          ->first();
        $lectiveYearId = $lectiveYearSelected->id ?? 6;
      } else {
        $lectiveYearId = intval($lectiveYearId);
      }

      // Consulta as classes e suas relações
      $model = Classes::join('users as u1', 'u1.id', '=', 'classes.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'classes.updated_by')
        ->leftJoin('users as u3', 'u3.id', '=', 'classes.deleted_by')
        ->leftJoin('schedule_types as sch_t', 'sch_t.id', '=', 'classes.schedule_type_id')
        ->leftJoin('room_translations as rt', function ($join) {
          $join->on('rt.room_id', '=', 'classes.room_id');
          $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('rt.active', '=', DB::raw(true));
        })
        ->leftJoin('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'classes.courses_id');
          $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('ct.active', '=', DB::raw(true));
        })
        ->leftJoin('lective_year_translations as lyt', function ($join) {
          $join->on('lyt.lective_years_id', '=', 'classes.lective_year_id');
          $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('lyt.active', '=', DB::raw(true));
        })
        ->select([
          'classes.*',
          'u1.name as created_by',
          'u2.name as updated_by',
          'u3.name as deleted_by',
          'rt.display_name as room',
          'sch_t.code as schedule',
          'ct.display_name as course',
          'lyt.display_name as lective_year'
        ])
        ->where('classes.lective_year_id', $lectiveYearId)
        ->get();

      $model = $model->groupBy('course');
      
      
      $model = collect($model)->map(function($item){
          return $this->ordena_plano($item);
      });
        
     $model = collect($model)->map(function($item){
          foreach($item as $data){
              $data->schedule = str_replace('-x', '', $data->schedule); 
          }
          return $item;
      });
        
      // Obtém as informações da instituição
      $institution = Institution::latest()->first();

      // Obtém os anos letivos e suas traduções
      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $lectiveYearId)
        ->select('*')
        ->get();

      // Define os títulos e informações do documento
      $titulo_documento = "Turmas" . " " . date("Y/m/d");
      $anoLectivo_documento = "Ano Acadêmico: " . $lectiveYears[0]->currentTranslation->display_name;
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      // Gera o PDF usando a view 'classes.pdf-relatorio'
      $pdf = PDF::loadView(
        'GA::classes.pdf-classes',
        compact(
          'model',
          'institution',
          'lectiveYears',
          'titulo_documento',
          'anoLectivo_documento',
          'documentoGerado_documento'
        )
      );

      // Configurações do PDF
      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '13.5mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 3000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'portrait');

      // Nome do arquivo PDF gerado intelige
      $pdf_name = "Turmas_" . $lectiveYears[0]->currentTranslation->display_name;

      // Retorna o PDF para visualização no navegador
      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception $e) {
      // Em caso de erro, registra no log e retorna uma resposta com o erro
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }
  
  
  public function ordena_plano($item){

        for($i=0; $i < count($item); $i++) {

            for($j=$i+1; $j < count($item); $j++) {

                
            $min = $i;
            // pegar os códigos dos objecto
            $objA = $item[$i]->code;
            $objB = $item[$j]->code;

            // pegar a substring apartir do 4 caractere
            $subA = substr($objA, 2);
            $subB = substr($objB, 2);

            
            if(strpos($subA, 'M') !== false)
            {
                 $subA = str_replace('M', '0', $subA);
            }    
            elseif(strpos($subA, 'T') !== false)
            {
                 $subA = str_replace('T', '1', $subA);
            }    
            else
            {
                 $subA = str_replace('N', '2', $subA);
            }
            
            
            if(strpos($subB, 'M') !== false)
            {
                 $subB = str_replace('M', '0', $subB);
            }   
            elseif(strpos($subB, 'T') !== false)
            {
                 $subB = str_replace('T', '1', $subB);
            } 
            else
            {
                 $subB = str_replace('N', '2', $subB);
            }
            
                 // convertendo em inteiros
                 $subA = intval($subA);
                 $subB = intval($subB);
                    
                   
                 // comparando
                 if($subB < $subA){
                    // Ordenar
                     $min = $j;
                 }

                 $aux = $item[$min];
                 $item[$min] = $item[$i];
                 $item[$i] = $aux;
                 continue;
    

        
            }

    }
         return $item;
}

}