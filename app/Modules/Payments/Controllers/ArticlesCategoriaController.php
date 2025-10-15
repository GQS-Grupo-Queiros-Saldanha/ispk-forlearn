<?php

namespace App\Modules\Payments\Controllers;

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

class ArticlesCategoriaController extends Controller
{
  public function index()
  {
    $category = DB::table('article_category')
      ->whereNull('deleted_at') // Adiciona filtro para não mostrar registros excluídos
      ->get();
    return view('Payments::articles.categoria.index', compact('category'));
  }

  public function ajax_list()
  {
    $data = DB::table('article_category as ct')
      ->leftJoin('users as cu', 'ct.created_by', '=', 'cu.id') // Join para o usuário que criou
      ->leftJoin('users as uu', 'ct.updated_by', '=', 'uu.id') // Join para o usuário que atualizou
      ->whereNull('ct.deleted_at') // Inclui apenas registros não excluídos
      ->select(
        'ct.id',
        'ct.name',
        'ct.created_at',
        'ct.updated_at',
        'cu.name as created_by', // Nome do usuário que criou
        'uu.name as updated_by'  // Nome do usuário que atualizou
      )
      ->get();

    return DataTables::of($data)
      ->addColumn('actions', function ($item) {
        return view('Payments::articles.categoria.datatables.actions')->with('item', $item);
      })
      ->editColumn('created_by', function ($item) {
        return $item->created_by;
      })
      ->editColumn('updated_by', function ($item) {
        return $item->updated_by;
      })
      ->rawColumns(['actions'])
      ->addIndexColumn()
      ->toJson();
  }


  private function insertCategory($name)
  {
    $auth = Auth::user()->id;

    // Capitaliza a primeira letra do nome da categoria
    $name = ucfirst(strtolower($name)); // Capitaliza a primeira letra e coloca o resto em minúsculas

    DB::table('article_category')->insert([
      'name' => $name,
      'created_at' => now(),
      'created_by' => $auth
    ]);
  }


  public function store(Request $request)
  {

    try {
      $this->insertCategory($request->name);

      Toastr::success(_('A criação da categoria foi realizada com sucesso.'), __('toastr.success'));
      return redirect()->back();
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }

  public function update(Request $request)
  {
    try {
      $auth = Auth::user()->id;

      DB::table('article_category')
        ->where('id', $request->chave)
        ->update([
          'name' => $request->name,
          'updated_at' => now(),
          'updated_by' => $auth
        ]);

      Toastr::success(_('Atualização foi realizada com sucesso'), __('toastr.success'));
      return redirect()->back();
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }

  public function destroy($id)
  {
    try {
      $auth = Auth::user()->id;

      DB::table('article_category')
        ->where('id', $id)
        ->update([
          'deleted_at' => now(),
          'deleted_by' => $auth,
          'updated_at' => now()
        ]);

      Toastr::success(_('Registro excluído com sucesso.'), __('toastr.success'));
      return response()->json(['success' => true]);
    } catch (Exception | Throwable $e) {
      Log::error($e);
      Toastr::error($e->getMessage(), __('toastr.error'));
      return redirect()->back();
    }
  }
}
