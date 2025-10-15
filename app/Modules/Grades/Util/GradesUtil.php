<?php

namespace App\Modules\Grades\Util;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use DB;
use Auth;
use Carbon\Carbon;
use App\Modules\Grades\Models\Grade;


class GradesUtil{
    
    public function getGrades($user_id,$discipline_id,$course_id ,$id_fase){
       $grade = Grade::where([
            'student_id' => $user_id,
            'discipline_id' => $discipline_id,
            'course_id' => $course_id,
            'id_fase' => $id_fase
        ])->first();
        return isset($grade->id) ? $grade->value : null; 
    }

    public function getUserInFaseStepOne($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma){
       $query = User::query()
            ->whereHas('roles', function ($q)  {
                $q->whereIn('id', [6,15]);
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
            ->leftjoin('grades as notas', function ($join) use($courseId,$id_disciplina) {
                $join->on('users.id', '=', 'notas.student_id')
                ->where('notas.course_id',$courseId)  
                ->where('notas.discipline_id',$id_disciplina);
            })
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
            ->join('user_classes as ut', function ($join) use($turma)  {
             $join->on('users.id', '=', 'ut.user_id')
             ->where('ut.class_id', $turma);
             }) 
             ->join('classes as tur', function ($join) use($turma)  {
             $join->on('tur.id', '=', 'ut.class_id')
             ->where('tur.id', $turma);
            }) 
         //  ->join('classes as tur', 'tur.id', '=', 'ut.class_id')
            ->leftJoin('lective_years', function ($join) {
                $join->whereRaw('users.created_at between `start_date` and `end_date`');
            })
            ->join('lective_year_translations as lyt', function ($join) {
             $join->on('lyt.lective_years_id', '=', 'lective_years.id');
             $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
             $join->on('lyt.active', '=', DB::raw(true));
             })->where('lective_years.id',$lectiveYears->id)
             ->where('tur.id', $turma)
            ->select([
             'users.*',
             'users.id',
             'ut.*',
             'tur.display_name',
             'notas.value',
             'u1.name as us_created_by',
             'full_name.value as name_completo',
             'u2.name as us_updated_by',
             // 'u3.name as deleted_by',
             'article_requests.status as state',
             'candidate.value as cand_number',
             'lyt.display_name as lective_year_code',
             'notas.id as nota_id'
            ])
            //->whereBetween('users.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
            ->distinct('article_requests.status')
            ->orderBy('name_completo');
            
        if(isset($lectiveCandidate->id)){
            $query = $query->where('notas.id_fase', $lectiveCandidate->id);
        }
        
        return $query;
    }

    public function getUserInFaseJoinGradesOne($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma){
        return $this->getUserInFaseStepOne($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma)->get();
    }


    public function getUserInFaseStepTwo($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma){
       return User::query()
            ->whereHas('roles', function ($q)  {
                  $q->whereIn('id', [6,15]);
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
            ->join('user_classes as ut', function ($join) use($turma)  {
                $join->on('users.id', '=', 'ut.user_id')
                ->where('ut.class_id', $turma);
            }) 
             ->join('classes as tur', function ($join) use($turma)  {
                $join->on('tur.id', '=', 'ut.class_id')
                ->where('tur.id', $turma);
            }) 
              ->leftJoin('lective_years', function ($join) {
                  $join->whereRaw('users.created_at between `start_date` and `end_date`');
              })
              ->join('lective_year_translations as lyt', function ($join) {
                  $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                  $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('lyt.active', '=', DB::raw(true));
              })->where('lective_years.id',$lectiveYears->id)
              ->select([
                  'users.*',
                  'users.id as user_id',
                  'u1.name as us_created_by',
                  'full_name.value as name_completo',
                  'u2.name as us_updated_by',
                  'u3.name as deleted_by',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code'
              ])
              ->distinct('article_requests.status')
             ->orderBy('name_completo');
    }

    public function getUserInFaseJoinGradesTwo($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma){
        return $this->getUserInFaseStepTwo($lectiveCandidate, $lectiveYears,$courseId,$id_disciplina,$turma)->get();
    }    


    public function getUserInFaseStudentGrades($lectiveCandidate,$lectiveYears,$courseId,$turma, $discip){
        return $this->getUserInFaseStudentGradesIds($lectiveCandidate->id,$lectiveYears,$courseId,$turma, $discip);
    }
    
    public function getUserInFaseStudentGradesIds($lectiveCandidateID,$lectiveYearsID,$courseId,$turma, $discip){
        
        return User::query()->whereHas('roles', function ($q)  { 
              $q->whereIn('id', [6,15]);
          })->with(['courses' => function ($q) use ($courseId) {
              $q->with(['currentTranslation']);
          }])
          ->join('users as u1', 'u1.id', '=', 'users.created_by')
          ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
          ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
          ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
          ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
          ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
                ->where('full_name.parameters_id', 1);
                //->orderBy('full_name.value');
        })
          ->leftJoin('user_parameters as candidate', function ($join) {
              $join->on('users.id', '=', 'candidate.users_id')
              ->where('candidate.parameters_id', 311);
          })

          ->join('user_courses as uc', function ($join) use($courseId)  {
              $join->on('users.id', '=', 'uc.users_id')
              ->where('uc.courses_id', $courseId);
          })

          ->leftJoin('grades as notas', function ($join) use ($courseId,$discip) {
            $join->on('uc.users_id', '=', 'notas.student_id')
            ->where('notas.course_id', $courseId);
         })->whereIn('notas.discipline_id',$discip)
        
        ->leftJoin('disciplines',function ($join) use ($discip) {
            $join->on('notas.discipline_id', '=', 'disciplines.id')
                 ->whereIn('notas.discipline_id', $discip);
         })
        
          ->join('user_classes as ut', function ($join) use($turma)  {
            $join->on('users.id', '=', 'ut.user_id')
            ->where('ut.class_id', $turma);
        }) 
        ->join('classes as tur', function ($join) use($turma)  {
            $join->on('tur.id', '=', 'ut.class_id')
            ->where('tur.id', $turma);
        })
        ->join('user_candidate as uca','uca.user_id','=','users.id')
        ->join('schedule_types as periodo','periodo.id','=', 'tur.schedule_type_id')
        ->join('schedule_type_translations as lyte', function ($join) {
                $join->on('lyte.schedule_type_id', '=', 'periodo.id');
                $join->on('lyte.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyte.active', '=', DB::raw(true));
        }) 
          ->leftJoin('lective_years', function ($join) {
              $join->whereRaw('users.created_at between `start_date` and `end_date`');
          })
          ->join('lective_year_translations as lyt', function ($join) {
              $join->on('lyt.lective_years_id', '=', 'lective_years.id');
              $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('lyt.active', '=', DB::raw(true));
          })
      
          ->where('lective_years.id',$lectiveYearsID)
          ->where('tur.id', $turma)
          ->where('uca.year_fase_id',$lectiveCandidateID)
          ->where('article_requests.status', 'total')
          ->select([
              'users.*',
              'users.id',
              'notas.value as nota',
              'notas.*',
              'lyte.description as periodo',
              'tur.display_name as turma',
              'tur.id as id_turma',
              'u1.name as us_created_by',
              'u2.name as us_updated_by',
              'full_name.value as name_completo',
              'article_requests.status as state',
              'candidate.value as cand_number',
              'lyt.display_name as lective_year_code',
              'disciplines.percentage'
          ])
         ->distinct('article_requests.status')
         ->orderBy('nota','DESC')
         ->orderBy('notas.discipline_id', 'asc')
         ->orderBy('candidate.value', 'asc');
    }

    public function getUserInFaseStudentGradesGet($lectiveCandidate,$lectiveYears,$courseId,$turma, $discip){
        return $this->getUserInFaseStudentGrades($lectiveCandidate,$lectiveYears,$courseId,$turma, $discip)->get();
    }

    public function modelFaseStudentListPauta($students ,$id_curso, $id_fase){
        $model = [];
        foreach($students  as $studant){
            if($studant->state != "pending"){
                $grade = DB::table('grades')->where([
                    ['course_id','=', $id_curso],
                    ['student_id' ,'=', $studant->student_id],
                    ['id_fase' ,'=', $id_fase]
                ])->first();    
                if(isset($grade->id)) array_push($model,$studant);
                else{
                    $uca = DB::table('user_candidate')->where([
                        ['user_id','=', $studant->student_id],
                        ['year_fase_id' ,'=', $id_fase]
                    ])->first();
                    if(isset($uca->id)) array_push($model,$studant);
                }
            }
        }            
        return $model;
    }


    public function insertVerify($data, $course, $discipline, $id_fase,$i){
        $params = [
            ['course_id','=',$course],
            ['discipline_id','=',$discipline],
            ['student_id','=',$data['students'][$i]],
            ['id_fase','=',$id_fase]
        ];
       $grade = DB::table('grades')->where($params)->first();
       
       if(isset($grade->id))  array_push($params,["id","=",$grade->id]);

        Grade::updateOrCreate($params,[
            'value' => (double)$data['grades'][$i],
            'updated_at'=>Carbon::now(),
            'updated_by'=> Auth::user()->id,
            'id_fase' => $id_fase
        ]);
       

    }

}