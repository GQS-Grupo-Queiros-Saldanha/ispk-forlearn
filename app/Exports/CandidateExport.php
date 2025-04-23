<?php

namespace App\Exports;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Grades\Models\Grade;
use App\Modules\Grades\Requests\GradeRequest;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Models\ParameterGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class CandidateExport implements FromView,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\FromView
    */
    public function view(): View
    {

        $request = Request()->all();
        $courseId = $request['id_curso'] ;
        $lectiveYears = $request['anoLectivo'] ;
        $id = $request['Id_disciplina'] ;

        $grade = Grade::where('discipline_id', $id)
              ->where('course_id', $courseId)
              ->get();
  
          
            $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

          $model = User::query()
            ->whereHas('roles', function ($q)  {
                  $q->whereIn('id', [15]);
              })->with(['courses' => function ($q) use ($courseId) {
                  $q->with([
                      'currentTranslation'
                      
                  ]);

              }])
              ->join('users as u1', 'u1.id', '=', 'users.created_by')
              ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
              ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
              ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
              ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
              
              ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
            })
              ->leftJoin('user_parameters as candidate', function ($join) {
                  $join->on('users.id', '=', 'candidate.users_id')
                  ->where('candidate.parameters_id', 311);
              })

              ->join('user_courses as uc', function ($join) use($courseId)  {
                  $join->on('users.id', '=', 'uc.users_id')
                  ->where('uc.courses_id', $courseId);
              })

              ->leftJoin('lective_years', function ($join) {
                  $join->whereRaw('users.created_at between `start_date` and `end_date`');
              })
              ->join('lective_year_translations as lyt', function ($join) {
                  $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                  $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('lyt.active', '=', DB::raw(true));
              })->where('lective_years.id',$lectiveYears)
              ->select([
                  'users.*',
                  'users.id',
                  'u1.name as us_created_by',
                  'u2.name as us_updated_by',
                  'full_name.value as name_completo',
                  // 'u3.name as deleted_by',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code',
                  
              ])

              

              ->distinct('article_requests.status')
               ->orderBy('name_completo')
              ->get();

            //   $model->courses->map(function ($course) {
            // })->implode(", ");
            $curso="";
            foreach($model[0]->courses as $cursoD){  
                $curso = $cursoD->currentTranslation->display_name;
            }

          $data = [
                'model' => $model,
                'curso'=>$curso,
                'notas'=>$grade
            ];           
           $notas=$grade;
             
      return view("Grades::exame.candidateExcel")->with($data);
  

    }
}
