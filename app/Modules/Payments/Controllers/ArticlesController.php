<?php

namespace App\Modules\Payments\Controllers;
use App\Modules\Payments\Models\ArticleDocument;
use App\Modules\GA\Models\DocumentsTypes; //model para os tipos de documentos
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleExtraFee;
use App\Modules\Payments\Models\ArticleMonthlyCharge;
use App\Modules\Payments\Models\ArticleTranslation;
use App\Modules\Payments\Requests\ArticleRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Log;
use PDF;
use Throwable;
use Toastr;
use Auth;
use Illuminate\Http\Request as HttpRequest;
use App\Model\Institution;
use App\Modules\Payments\Util\ArticlesUtil;
use App\Modules\Payments\Models\Transaction;


class ArticlesController extends Controller{

    private $articlesUtil;

    function __construct(){
        $this->articlesUtil = new ArticlesUtil();
    }

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

            return view("Payments::articles.index", compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

  public function ajax(){
        try {
            $currentData = Carbon::now();
            $anoLectivo=DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->get();
            
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();

            $model = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'articles.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('article_category as ac', 'ac.id', '=', 'articles.id_category')
                ->select([
                    'articles.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'at.display_name',
                    'at.acronym',
                    'ac.name as category_name'
                ])
                ->whereNull('articles.deleted_by')
                ->whereNull('articles.deleted_at')
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item)use($anoLectivo) {
                    return view('Payments::articles.datatables.actions',compact('item','anoLectivo'));
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
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function createEmolimento($idAno_lectivo)
    {
        try {
            
            $courses = Course::with([
                'currentTranslation'
            ])->get();
                $tiposdocumentos = DB::table('documentation_type')->get();
                $categorias = DB::table('article_category')->get();
               $data = [
                'action' => 'create',
                'categorias' => $categorias,
                'courses' => $courses,
                'idAno_lectivo' => $idAno_lectivo,
                'tiposdocumentos' => $tiposdocumentos,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Payments::articles.article')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    protected function storeArticleRelations(ArticleRequest $request, Article $article)
    {
        // Extra Fees
        if ($request->has('extra_fees_percent')) {
            $extra_fees = [];
            foreach ($request->get('extra_fees_percent') as $idx => $fee_percent) {
                $extra_fees[] = [
                    'article_id' => $article->id,
                    'fee_percent' => $fee_percent,
                    'max_delay_days' => $request->get('extra_fees_delay')[$idx]
                ];
            }
            if (!empty($extra_fees)) {
                ArticleExtraFee::insert($extra_fees);
            }
        }

        // Monthly Charge
        if ($request->has('monthly_charge_course')) {
            $monthly_charges = [];
            foreach ($request->get('monthly_charge_course') as $idx => $course) {
                $monthly_charges[] = [
                    'article_id' => $article->id,
                    'course_id' => $course,
                    'course_year' => $request->get('monthly_charge_course_year')[$idx],
                    'start_month' => $request->get('monthly_charge_start_month')[$idx],
                    'end_month' => $request->get('monthly_charge_end_month')[$idx],
                    'charge_day' => $request->get('monthly_charge_charge_day')[$idx],
                ];
            }
            if (!empty($monthly_charges)) {
                ArticleMonthlyCharge::insert($monthly_charges);
            }
        }
    }

    protected function destroyArticleRelations(Article $article)
    {
        $article->extra_fees()->delete();
        $article->monthly_charges()->delete();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequest $request
     * @return Response
     */
    public function store(ArticleRequest $request)
    {
        try {
            // return $request;
            DB::beginTransaction();
              $lectiveYearSelected = LectiveYear::whereId($request->idAno_lectivo)->first();
            // ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
            // Create
            $article = new Article([
                'code' => $request->get('code'),
                'base_value' => $request->get('base_value'),
                'id_category' => $request->get('categoria'),
                'sigla' => $request->get('sigla'),
                'code_reference_discipline' => $request->get('customRadioInline'),
                'created_at' =>  $lectiveYearSelected->start_date,
                'anoLectivo' =>  $request->idAno_lectivo
            ]);

            $article->save();
            
            $this->storeArticleRelations($request, $article);

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $article_translations[] = [
                    'article_id' => $article->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'observation' => $request->get('observation')[$language->id],
                    'acronym' => $request->get('acronym')[$language->id],
                    'created_at' => $lectiveYearSelected->start_date,
                ];
            }

            if (!empty($article_translations)) {
                ArticleTranslation::insert($article_translations);
            }

            DB::commit();
            
            if ($request->has('documentation_type_id') && !empty($article)) {
                $doc = new ArticleDocument();
                $doc->article_id = $article->id;
                $doc->documentation_type_id = $request->input('documentation_type_id');
                $doc->created_by = auth()->id() ?? 1;
                $doc->created_at = now();
                $doc->save();
            }

            // Success message
            Toastr::success(__('Payments::articles.store_success_message'), __('toastr.success'));
            return redirect()->route('articles.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function fetch($id, $action)
    {
        try {
            // Find
            $article = Article::whereId($id[1])
                ->with([
                    'monthly_charges' => function ($q) {
                        $q->with([
                            'course' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    }
                ])
                ->with('extra_fees')
                ->firstOrFail();
            $categorias = DB::table('article_category')->get();
            $tiposdocumentos = DB::table('documentation_type')->get();
            $data = [
                'action' => $action,
                'article' => $article,
                'categorias' => $categorias,
                'idAno_lectivo' => $id[0],
                'tiposdocumentos' => $tiposdocumentos,
                'translations' => $article->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Payments::articles.article')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::articles.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($array)
    {
        $id=explode(',',$array);
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($array)
    {
        $id=explode(',',$array);
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
     * @param ArticleRequest $request
     * @param int $id
     * @return Response
     */
    public function update(ArticleRequest $request, $id)
    {
       
        try {
           
            DB::beginTransaction();
            $lectiveYearSelected = LectiveYear::whereId($request->idAno_lectivo)->first();
            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find
            $article = Article::whereId($id)->firstOrFail();

            // Update
            $article->code = $request->get('code');
            $article->base_value = $request->get('base_value');
            $article->id_category = $request->get('categoria');
            $article->code_reference_discipline = isset($request->emolument_disciplina)? $request->emolument_disciplina:null;
            
            
        

            // Delete all relations
            $this->destroyArticleRelations($article);

            // Associate new relations
            $this->storeArticleRelations($request, $article);

            // Disable previous translations
            ArticleTranslation::where('article_id', $article->id)->update(['active' => false]);

            $version = ArticleTranslation::where('article_id', $article->id)
                    ->where('language_id', $default_language->id)
                    ->count() + 1;

            // Add new translations
           
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $article_translations[] = [
                    'article_id' => $article->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'observation' => $request->get('observation')[$language->id] ?? null,
                    'acronym' => $request->get('acronym')[$language->id] ?? null,
                    'created_at' => $lectiveYearSelected->start_date,
                    'version' => $version,
                    'active' => true,
                ];
            }
            if (!empty($article_translations)) {

                ArticleTranslation::insert($article_translations);
            }

            DB::commit();

            $article->save();

            if (!empty($article)) {
                $doc = new ArticleDocument();
                $doc->article_id = $article->id;
                $doc->documentation_type_id = $request->input('documentation_type_id');
                $doc->created_by = auth()->id() ?? 1;
                $doc->created_at = now();
                $doc->save();
            }

            // Success message
            Toastr::success(__('Payments::articles.update_success_message'), __('toastr.success'));
            return redirect()->route('articles.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::articles.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($array)
    {
        try {
            $vetor=explode(',',$array);
            $id=$vetor[1];
            // return $id;
            $article = Article::whereId($id)->firstOrFail();

            if ($article->payments()->count() > 0) {
                Toastr::error(__('Payments::articles.destroy_error_message'), __('toastr.error'));
                return redirect()->back();
            } else {
                DB::beginTransaction();

                // Delete translations
                $article->translations()->delete();

                // Delete all relations
                $this->destroyArticleRelations($article);

                $article->delete();
                // null out code so it can be used again and
                $article->code = null;

                $article->deleted_by = Auth::user()->id;

                // update DB row to force update to delete_by
                $article->save();

                DB::commit();

                // Success message
                Toastr::success(__('Payments::articles.destroy_success_message'), __('toastr.success'));
                return redirect()->route('articles.index');
            }
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::articles.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function duplicateListItem($id)
    {
        try {
            $action = "edit";
            // Find
            // Find
            $article = Article::whereId($id)
                ->with([
                    'monthly_charges' => function ($q) {
                        $q->with([
                            'course' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    }
                ])
                ->with('extra_fees')
                ->firstOrFail();

            $data = [
                'action' => $action,
                'article' => $article,
                'translations' => $article->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];

            return response()->json($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-editions.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }


    protected function storeArticleRelationsDuplicated(HttpRequest $request, Article $article)
    {
        // Extra Fees
        if ($request->has('extra_fees_percent')) {
            $extra_fees = [];
            foreach ($request->get('extra_fees_percent') as $idx => $fee_percent) {
                $extra_fees[] = [
                    'article_id' => $article->id,
                    'fee_percent' => $fee_percent,
                    'max_delay_days' => $request->get('extra_fees_delay')[$idx]
                ];
            }
            if (!empty($extra_fees)) {
                ArticleExtraFee::insert($extra_fees);
            }
        }

        // Monthly Charge
        if ($request->has('monthly_charge_course')) {
            $monthly_charges = [];
            foreach ($request->get('monthly_charge_course') as $idx => $course) {
                $monthly_charges[] = [
                    'article_id' => $article->id,
                    'course_id' => $course,
                    'course_year' => $request->get('monthly_charge_course_year')[$idx],
                    'start_month' => $request->get('monthly_charge_start_month')[$idx],
                    'end_month' => $request->get('monthly_charge_end_month')[$idx],
                    'charge_day' => $request->get('monthly_charge_charge_day')[$idx],
                ];
            }
            if (!empty($monthly_charges)) {
                ArticleMonthlyCharge::insert($monthly_charges);
            }
        }
    }
    public function duplicateArticle(HttpRequest $request)
    {
        // return $request;
        try {
            DB::beginTransaction();

            // Create
            $article = new Article([
                'code' => $request->get('code'),
                'base_value' => $request->get('base_value')
            ]);

            $article->save();

            $articleValue = Article::whereId($request->get('id'))->firstOrFail();

            $this->storeArticleRelationsDuplicated($request, $articleValue);

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $article_translations[] = [
                    'article_id' => $article->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                ];
            }

            if (!empty($article_translations)) {
                ArticleTranslation::insert($article_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Payments::articles.store_success_message'), __('toastr.success'));
            return redirect()->route('articles.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }
    public function article_duplicar(HttpRequest $request)
    {
        // return $request;
        $boolem=false;
       $anoLectivo=DB::table('lective_years')
            ->whereId($request->lective_years)
            ->first(); 
            $start=substr($anoLectivo->start_date, 0, 4);
            $end=substr($anoLectivo->end_date, 0, 4);
            foreach ($request->articleCopy as $key => $item) {
                 $getArticle=DB::table('articles as article')
                    ->join('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'article.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->select([
                        'article.base_value',
                        'article.id_code_dev',
                        'at.article_id as translation_article',
                        'at.language_id',
                        'at.display_name',
                        'at.description',
                        'at.version',
                        'at.active'
                        ])
                        ->where('article.id',$item)
                        ->whereNull('article.deleted_at')
                ->first();

                $getArticleCurso=DB::table('articles as article')
                ->join('article_monthly_charges as art_monthly_charge','art_monthly_charge.article_id','=','article.id')
                ->select([
                        'art_monthly_charge.article_id as art_monthly_charge_article_id',
                        'art_monthly_charge.course_id',
                        'art_monthly_charge.course_year',
                        'art_monthly_charge.start_month',
                        'art_monthly_charge.end_month',
                        'art_monthly_charge.charge_day',
                    ])
                ->where('article.id',$item)
                ->whereNull('article.deleted_at')
                ->get();

                $getArticleTaxa=DB::table('articles as article')
                ->join('article_extra_fees as article_extra_fee','article_extra_fee.article_id','=','article.id')
                ->select([
                        'article_extra_fee.article_id as article_extra_fee_article_id',
                        'article_extra_fee.fee_percent',
                        'article_extra_fee.max_delay_days'
                    ])
                ->where('article.id',$item)
                ->whereNull('article.deleted_at')
                ->get();

                $getArticleTheSame=DB::table('articles as article')
                    ->join('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'article.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->whereNull('article.deleted_at')
                    ->where('at.display_name','=',$getArticle->display_name)
                    ->whereBetween('article.created_at', [$anoLectivo->start_date, $anoLectivo->end_date])
                ->get();
                if ($getArticleTheSame->isEmpty()) {
                    $boolem=true;
                    $code= substr($getArticle->display_name, 0, 4).'_'.$start.'_'.$end;
                   $getid_article=DB::table('articles')->insertGetId([
                        'code' => $code,
                        'base_value' => $getArticle->base_value,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $anoLectivo->start_date,
                        'updated_at' => Carbon::Now(),
                        'id_code_dev' => $getArticle->id_code_dev,
                        'anoLectivo' =>  $request->lective_years
                    ]);
                    DB::table('article_translations')->insert([
                        'article_id' => $getid_article,
                        'language_id' => $getArticle->language_id,
                        'display_name' => $getArticle->display_name,
                        'description' => $getArticle->description,
                        'version' => $getArticle->version,
                        'active' => $getArticle->active,
                        'created_at' =>$anoLectivo->start_date,
                        'updated_at' => Carbon::Now()
                    ]);

               
                    if(!$getArticleCurso->isEmpty()){
                        foreach ($getArticleCurso as $key => $elementCurso) {
                            DB::table('article_monthly_charges')->insert([
                                'article_id' => $getid_article,
                                'course_id' => $elementCurso->course_id,
                                'course_year' => $elementCurso->course_year,
                                'start_month' => $elementCurso->start_month,
                                'end_month' => $elementCurso->end_month,
                                'charge_day' => $elementCurso->charge_day,
                            ]);
                        }

                    }if(!$getArticleTaxa->isEmpty()){
                        foreach ($getArticleTaxa as $key => $elementTaxa) {
                            DB::table('article_extra_fees')->insert([
                                'article_id' => $getid_article,
                                'fee_percent' => $elementTaxa->fee_percent,
                                'max_delay_days' => $elementTaxa->max_delay_days
                            ]);
                        }
                    }
                }
            }
            if ($boolem==true) {
                Toastr::success(__('Emolumentos/propinas copiado com sucesso'), __('toastr.success'));
                return redirect()->back();
            }else{
                Toastr::error(__('Emolumentos/propinas Já existe neste ano'), __('toastr.error'));
                return redirect()->back();
            }
            
    }

    public function ArticleBy($lective_year)
    {
        try{

            $currentData = Carbon::now();
            $anoLectivo=DB::table('lective_years')->whereId($lective_year)->get();               

            $lectiveYearSelected = LectiveYear::whereId($lective_year)->first();

            $model = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'articles.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('article_category as ac', 'ac.id', '=', 'articles.id_category')
                ->select([
                    'articles.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'at.display_name',
                    'at.acronym',
                    'ac.name as category_name',
                ])
                ->whereNull('articles.deleted_by')
                ->whereNull('articles.deleted_at')
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item)use($anoLectivo) {
                    return view('Payments::articles.datatables.actions',compact('item','anoLectivo'));
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    public function implementar_regra($id_anolectivo){
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
                        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                        ->first();
        $lectiveSelected = LectiveYear::whereId($id_anolectivo)->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $lista_Month=[];
        $ordem_Month=[];
        $desor_Month=[];
        $getLocalizedMonths=getLocalizedMonths();
        
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id']>7 && $value['id']<10) {
            }else{
                $lista_Month[]=$value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id']>9) {
                $ordem_Month[]=$item;
            } else {
                $desor_Month[]=$item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[]=$element;
        }

        $model = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
        ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
        ->leftJoin('article_translations as at', function ($join) {
            $join->on('at.article_id', '=', 'articles.id');
            $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('at.active', '=', DB::raw(true));
        })
        ->select([
            'articles.*',
            'u1.name as created_by',
            'u2.name as updated_by',
            'u3.name as deleted_by',
            'at.display_name',
        ])
        ->whereBetween('articles.created_at', [$lectiveSelected->start_date, $lectiveSelected->end_date])
        ->get();
    
        $scheduleTypes = DB::table('schedule_types as st')
        ->join('schedule_type_translations as stt','stt.schedule_type_id','st.id')
        ->where('stt.active',1)
        ->where('st.deleted_at')
        ->where('st.deleted_by')
        ->select('stt.display_name', 'st.id')
        ->get();

        return view("Payments::articles.rulesArticle", compact('lectiveYears', 'lectiveYearSelected','id_anolectivo','ordem_Month','model', 'scheduleTypes'));
    }
    public function createRegraEmolumento(HttpRequest $request)
    {
         
        $currentData = Carbon::now();
       
        if (isset($request->emolument)) {
        //    return $request; 
           foreach ($request->emolument as $key => $item) {
                $getConsulta=DB::table('artcles_rules as art_rule')
                ->where('art_rule.id_articles','=',$item)
                ->where('art_rule.deleted_by','=',null)
                ->where('art_rule.deleted_at','=',null)
                ->where('art_rule.mes','=',$request->month)
                ->where('art_rule.ano_lectivo','=',$request->lective_year)
                ->get();
                if (count($getConsulta)>0) {
                     // Erro message
                     Toastr::error(__('Erro ao impmlementar nova regra! caso já criado'), __('toastr.error'));
                } else {

                    $created_rules=DB::table('artcles_rules')->insert([
                        'id_articles' => $item,
                        'mes' => $request->month,
                        'valor' => $request->valorPercentual,
                        'ano_lectivo' => $request->lective_year,
                        'created_by' => Auth::user()->id,
                        'update_by' =>null,
                        'deleted_by' =>null,
                        'created_at'=>$currentData

                    ]);
                    
                    // Success message
                    Toastr::success(__('Regra Implementado com sucesso'), __('toastr.success'));
                    
                }
                
                return redirect()->back();
            }
        } else {
            $getConsulta=DB::table('artcles_rules as art_rule')
                ->where('art_rule.id_articles','=',null)
                ->where('art_rule.mes','=',$request->month)
                ->where('art_rule.deleted_by','=',null)
                ->where('art_rule.deleted_at','=',null)
                ->where('art_rule.ano_lectivo','=',$request->lective_year)
                ->get();
            if (count($getConsulta)>0) {
                // Erro message
                Toastr::error(__('Erro ao impmlementar nova regra! caso já criado'), __('toastr.error'));
           } else {
                $created_rules=DB::table('artcles_rules')->insert([
                    'id_articles' => null,
                    'mes' => $request->month,
                    'valor' => $request->valorPercentual,
                    'ano_lectivo' => $request->lective_year,
                    'created_by' => Auth::user()->id,
                    'update_by' =>null,
                    'deleted_by' =>null,
                    'created_at'=>$currentData

                ]);
                // Success message
                Toastr::success(__('Regra Implementado com sucesso'), __('toastr.success'));
            }
            return redirect()->back();
            
        }
        
    }
    
    public function getImplemtRules($id_anolectivo) {
        try {    
            $getConsulta = $this->articlesUtil->getArticleRules($id_anolectivo);  
            return response()->json(['data'=>$getConsulta]);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };

    }


    public function createRegraEmolumentoNew(Request $request, $id_anolectivo){
        if(!$this->articlesUtil->validRequest($request)) return back();
        foreach($request->emolument as $emolument){
            foreach($request->month as $month){
                foreach($request->schedule_type as $schedule_type){
                    foreach($request->ano_curricular as $ano_curricular){
                        $data = [
                            'schedule_type_id' => $schedule_type,
                            'ano_curricular' => $ano_curricular,
                            'ano_lectivo' => $id_anolectivo,
                            'id_articles' => $emolument,
                            'mes' => $month,
                            'deleted_by' => null,
                            'deleted_at' => null,
                        ];

                        $ruleArticle = DB::table('artcles_rules')->where($data)->first();
                        $data['valor'] = $request->valorPercentual;

                        $data['updated_by'] = auth()->user()->id;
                        $data['updated_at'] = now();

                        if(!isset($ruleArticle->id)){
                            $data['created_by'] = auth()->user()->id;
                            $data['created_at'] = now();
                            DB::table('artcles_rules')->insert($data);
                        }else{
                            DB::table('artcles_rules')->whereId($ruleArticle->id)->update($data);
                        }

                    }
                }
            }
        }
        Toastr::success(__("Regras foram implementadas com successo"), __('toastr.success'));
        return back();
    }

    public function getImplemtRulesAjax($id_anolectivo) {
        $model = $this->articlesUtil->getArticleRules($id_anolectivo);
        return Datatables::of($model)
        ->addColumn('actions', function ($item){
             return view('Payments::articles.datatables.rules-article',compact('item'));
        })
        ->rawColumns(['actions'])
        ->addIndexColumn()
        ->toJson();
    }

    public function delRegraEmolumento($id){
        try{
            DB::table('artcles_rules')->where('id', $id)->update([
                "deleted_by" => auth()->user()->id,
                "deleted_at" => Carbon::now(),
            ]);
            Toastr::success(__('A regra foi eliminado com successo'), __('toastr.success'));
        }catch(Exception  $e){
            Toastr::warning(__('Não foi possível eliminar a regra'), __('toastr.warning'));
        }
        return back();
    }

    public function getImplemtRulesAnoLectivo($ano_lectivo)
    {
        try { 
            $getConsulta=DB::table('artcles_rules as art_rule')
                 ->leftJoin('articles as art','art.id','=','art_rule.id_articles')
                // ->where('art_rule.id_articles','=',$item)
                // ->where('art_rule.mes','=',$request->month)
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'art.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->where('art_rule.ano_lectivo','=',$ano_lectivo)
                ->whereNull('art_rule.deleted_by')
                ->select([
                    'art_rule.id as id_ruleArtc',
                    'art_rule.valor as valor',
                    'art_rule.estado as estado',
                    'art_rule.mes as mes',
                    'art_rule.created_at as created_at',
                    'at.display_name as display_name'
                ])
                ->get();  
                
               
                
           
            return response()->json(['data'=>$getConsulta]);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function updateDeleteRules($id_articlRules)
    {
        $consulteAtive=DB::table('artcles_rules')
        ->Where('artcles_rules.estado','=',1)
        ->Where('artcles_rules.id','=',$id_articlRules)
        ->WhereNull('artcles_rules.deleted_by')
        ->get();
        if (count($consulteAtive)>0) {
            return response()->json(['data'=>2]);
        } 
        else {
            $apdateRules = DB::table('artcles_rules')
              ->where('id', $id_articlRules)
              ->update(['deleted_by' => Auth::user()->id]);
              return response()->json(['data'=>$apdateRules]);
        }
        
    
    }

    //Criado pelo Marcos
    public function getEmoluAnoletivo($anoLectivo)
    {
        $lectiveYearSelected = LectiveYear::whereId($anoLectivo)->first();
        
        $artiles = Article::with([
            'currentTranslation',
            'extra_fees',
            'monthly_charges'
            ])->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->get();
            return response()->json($artiles);
    }


    public function getEmolumentoValor($emolu, $id_anolectivo) {
               
        // $artiles = [$id_anolectivo, $emolu];
        
        // return response()->json($artiles);

        try { 

            $getConsulta=DB::table('artcles_rules as art_rule')
                 ->leftJoin('articles as art','art.id','=','art_rule.id_articles')
                // ->where('art_rule.id_articles','=',$item)
                // ->where('art_rule.mes','=',$request->month)
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'art.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->where('art_rule.ano_lectivo','=',$id_anolectivo)
                ->whereNull('art_rule.deleted_by')
                ->select([
                    'art_rule.id as id_ruleArtc',
                    'art_rule.valor as valor',
                    'art_rule.mes as mes',
                    'art_rule.created_at as created_at',
                    'at.display_name as display_name'
                ])
                ->get();  
                
               
                
           
            return response()->json(['data'=>$getConsulta]);
      
        } catch (Exception | Throwable $e) {
            // return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }


    public function getEmolumentoValorUpdate(Request $request) {
        
       
        try { 
            // return  $request;
            $id = $request->idRegra; 
            $month = $request->month; 
 
            $consulta = DB::table('artcles_rules as art_rule')
            ->where('art_rule.mes','=',$month)
            ->where('art_rule.id','!=',$id)
            ->where('art_rule.ano_lectivo','=',$request->anoLectivo)
            ->whereNull('art_rule.deleted_by')
            ->get();
            if (count($consulta) > 0) {
                // Erro message
                Toastr::error(__('Erro ao edita nova regra! caso já exitente'), __('toastr.error'));
                
            }else {

                $consultaRules = DB::table('artcles_rules as art_rule')
                ->where('art_rule.estado','=',1)
                ->where('art_rule.id','=',$id)
                ->where('art_rule.ano_lectivo','=',$request->anoLectivo)
                ->whereNull('art_rule.deleted_by')
                ->get();
                
                if (count($consultaRules) > 0) {
                    Toastr::error(__('Erro ao edita nova regra! não é permitido editar esta regra já utilizada'), __('toastr.error'));
                } else {
                    $update =  DB::table('artcles_rules as art_rule')
                        ->where('art_rule.mes', $id)
                        ->where('art_rule.mes', $month)
                        ->update([
                            'art_rule.valor' => $request->valorPercentual,
                            'art_rule.mes' => $month
                        ]);
                        Toastr::success(__('Regra editada com sucesso'), __('toastr.success'));
                }
                
             

            }
            return redirect()->back() ;
            //return response()->json(['data'=>$getConsulta]);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }

    public function updateTransain()
    {
        try{
        
        $date1='2022-03-25';
        $date2='2022-03-25';
        $valoresRecibo=array();
        $date_from = Carbon::parse($date1)->startOfDay();
        $date_to = Carbon::parse($date2)->endOfDay();

            // $getTransaction=DB::table('transactions as trans')
            //         ->join('transaction_article_requests as trans_articl_reques','trans_articl_reques.transaction_id','=','trans.id')
            //         ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
            //         ->leftJoin('article_translations as at', function ($join) {
            //             $join->on('at.article_id', '=', 'article_reques.article_id');
            //             $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
            //             $join->on('at.active', '=', \DB::raw(true));
            //         })
            //         ->join('users as us', 'us.id', '=', 'article_reques.user_id')
            //         ->leftJoin('user_parameters as full_name', function ($join) {
            //             $join->on('us.id', '=', 'full_name.users_id')
            //             ->where('full_name.parameters_id', 1);
            //         })
            //         ->leftJoin('user_parameters as up_meca', function ($join) {
            //             $join->on('us.id', '=', 'up_meca.users_id')
            //             ->where('up_meca.parameters_id', 19);
            //         })
            //         ->join('transaction_info as info_trans', 'info_trans.transaction_id', '=', 'trans.id')
                    
            //         ->join('banks as bank', 'info_trans.bank_id', '=', 'bank.id')

            //         ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
            //         ->leftJoin('user_parameters as user_va', function ($join) {
            //             $join->on('u1.id', '=', 'user_va.users_id')
            //             ->where('user_va.parameters_id', 1);
            //         })
            //         ->leftJoin('transaction_receipts as recibo', function ($join) {
            //             $join->on('recibo.transaction_id', '=', 'trans.id');
            //         })
            //         ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
            //             $join->on('historic_saldo.id_transaction', '=', 'trans.id');
            //         })
            //         ->select([
            //             'trans.id as transaction_id',
            //             'trans.created_at as created_atranst',
            //             'trans.data_from as data_from',
            //             'trans.type as transaction_type',
            //             'article_reques.id as id_article_requests',
            //             'article_reques.status as status',
            //             'at.display_name as article_name',
            //             'article_reques.month as month',
            //             'article_reques.base_value as base_value',
            //             'full_name.value as full_name',
            //             'recibo.id as id_recibo',
            //             'recibo.code as recibo',
            //             'recibo.path as path',
            //             'bank.display_name as bank_name',
            //             'bank.id as id_bank',
            //             'up_meca.value as matriculation_number',
            //             'u1.name as created_by_user',
            //             'info_trans.fulfilled_at as fulfilled_at',
            //             'info_trans.value as valorreferencia',
            //             'historic_saldo.valor_credit as valorSaldo_credit',
            //             'trans_articl_reques.value as price',
            //             'info_trans.reference as reference'
            //         ])
            //         ->distinct('trans.transaction_id')
            //         ->whereBetween('trans.created_at', [$date_from, $date_to])
            //         ->whereNull('article_reques.deleted_by')
            //         ->where('trans.data_from','!=','estorno')
            //         ->where('recibo.path','!=', null)
            //         ->where('recibo.code','!=', null)
            // ->get();
            //  foreach ($getTransaction  as $key => $item) {
            //      if (empty($valoresRecibo)) {
            //          $item->{'recibo_repetido'}="N/A";
            //          $valoresRecibo[]=(object)['recibo'=>$item->recibo,'path'=>$item->path,'transaction_id'=>$item->transaction_id];
            //      }else{
            //         if ($item->recibo==$valoresRecibo[$key-1]->recibo && $item->transaction_id!=$valoresRecibo[$key-1]->transaction_id) {
            //             $item->{'recibo_repetido'}="repetico- ".$item->recibo ;
            //             $valoresRecibo[]=(object)['recibo repetido'=>"repetido- ".$item->recibo,'recibo'=>$item->recibo,'path'=>$item->path,'transaction_id'=>$item->transaction_id];
                   
            //         } else {

            //             $item->{'recibo_repetido'}="N/A";
            //             $valoresRecibo[]=(object)['recibo'=>$item->recibo,'path'=>$item->path,'transaction_id'=>$item->transaction_id];
            //         }
                
                     
                    
                 
            //     }
            //  }
            //  return $getTransaction;
            //  return $valoresRecibo;
           
            $getAll_studentMore_credit_saldo=DB::table('users')
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->where('credit_balance','>=','1')
            ->orderBY('credit_balance','DESC')
            ->get();
                    
                
        // return view('Payments::articles.updateTransacao',compact('getAll_studentMore_credit_saldo'));
        $pdf = PDF::loadView('Payments::articles.updateTransacao',compact('getAll_studentMore_credit_saldo'));
            $pdf->setOption('margin-top', '0mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '1cm');
            // $pdf->save('recibo/'.$referencia.'_recibo.pdf');  
           return $pdf->stream(); 

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }


    public function updateTransainEstorno(Request $request)
    {
        return $request;
    }
    
    
    
    
     public function confiDivida(){
        try{
            return view('Payments::articles.configDivida.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }
    
    
     public function confDividaCreate(Request $request)
    {   
        try{
            // return $request;
        $insert=DB::table('config_divida_instituicao')
        ->insert([
            'qtd_divida'=>$request->qdt_divida,
            'dias_exececao'=>$request->qdt_dias,
            'status'=>'pandding',
            'updated_at'=>Carbon::Now(),
            'created_at'=>Carbon::Now(),
            'updated_by'=>  Auth::user()->id,
            'created_by'=>  Auth::user()->id,
        ]);

            Toastr::success(__('Regra criada com sucesso'), __('toastr.success'));
            return redirect()->back() ;
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }
    public function configDividaAjax()
    {
        try{    
                $select=DB::table('config_divida_instituicao as config_divida')
                ->leftJoin('user_parameters as full_nameCreated',function ($q)
                {
                    $q->on('full_nameCreated.users_id','=','config_divida.created_by')
                    ->where('full_nameCreated.parameters_id',1);
                })
                ->leftJoin('user_parameters as full_nameUpdate',function ($q)
                {
                    $q->on('full_nameUpdate.users_id','=','config_divida.updated_by')
                    ->where('full_nameUpdate.parameters_id',1);
                })
                ->select([
                    'config_divida.id',
                    'config_divida.status',
                    'config_divida.qtd_divida',
                    'config_divida.dias_exececao',
                    'config_divida.created_at',
                    'config_divida.updated_at',
                    'full_nameCreated.value as created_by',
                    'full_nameUpdate.value as updated_by'
                ])
                ->whereNull('config_divida.deleted_at')
                ->whereNull('config_divida.deleted_by');

                return Datatables::of($select)
                ->addColumn('actions', function ($item){
                     return view('Payments::articles.configDivida.datatables.actions',compact('item'));
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ativarConfigDivida($id)
    {   try{

            $updateAll= DB::table('config_divida_instituicao')
            ->where('status','=','ativo')
            ->update(['status' => 'panding']); 

            $update= DB::table('config_divida_instituicao')
            ->where('id',$id)
            ->update(['status' => 'ativo']);
            
            Toastr::success(__('Regra Ativada com sucesso'), __('toastr.success'));
            return redirect()->back() ;

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    
    }

    public function deleteDividaConfiguracao(Request $request)
    {
       
        try{

            $update= DB::table('config_divida_instituicao')
            ->where('id',$request->getId)
            ->update([
                'status' => 'panding',
                'deleted_at' => Carbon::Now(),
                'deleted_by' => Auth::user()->id
            ]);
            
            Toastr::success(__('Regra Eliminda com sucesso'), __('toastr.success'));
            return redirect()->back() ;
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        };
    }
    
    public function categoria()
  {
    return view('Payments::articles.categoria.index');
  }
  
  public function gerarPDF()
  {
    try {
      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->first();

      $model = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
        ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
        ->leftJoin('article_translations as at', function ($join) {
          $join->on('at.article_id', '=', 'articles.id');
          $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('at.active', '=', DB::raw(true));
        })
        ->leftJoin('article_category as ac', 'ac.id', '=', 'articles.id_category')
        ->select([
          'articles.*',
          'u1.name as created_by',
          'u2.name as updated_by',
          'u3.name as deleted_by',
          'at.display_name',
          'at.observation', // Adicionando campo 'observation'
          'at.acronym', // Adicionando campo 'acronym'
          'ac.name as category_name',
          'ac.id as category_id',
        ])
        ->whereNull('articles.deleted_by')
        ->whereNull('articles.deleted_at')
        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->orderBy('ac.id')
         ->orderBy('at.display_name')
        ->get()
        ->groupBy('category_name'); // Agrupa por categoria

      $institution = Institution::latest()->first();
      $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->where('id', $lectiveYearSelected->id)
        ->select('*')
        ->get();

      $titulo_documento = "Tabela de Emolumento" . date("Y/m/d");
      $anoLectivo_documento = "Ano Acadêmico: ";
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      $pdf = PDF::loadView(
        'Payments::articles.pdf-relatorio', // Substitua pelo caminho correto da sua view
        compact(
          'model',
          'institution',
          'lectiveYears',
          'titulo_documento',
          'anoLectivo_documento',
          'documentoGerado_documento'
        )
      );

      $pdf->setOption('margin-top', '5mm');
      $pdf->setOption('margin-left', '5mm');
      $pdf->setOption('margin-bottom', '5mm');
      $pdf->setOption('margin-right', '5mm');
      $pdf->setOption('enable-javascript', false);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', false);
      $pdf->setPaper('a4', 'portrait');

      $pdf_name = "Tabela_de_Emolumento_" . $lectiveYears[0]->currentTranslation->display_name;

      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }



}
