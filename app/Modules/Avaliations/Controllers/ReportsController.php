<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Department;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    public function index()
    {
        return view("Avaliations::reports.reports");
    }

    public function getAllCourses()
    {
        $courses = Course::with([
                'currentTranslation'
            ])->get();

        return response()->json($courses);
    }

    public function getAllDepartments()
    {
        $departments = Department::with([
            'currentTranslation'
        ])->get();

        return response()->json($departments);
    }

    public function allTeachersWithGrades()
    {
        try {
            $model = User::query()
                ->join('users as teacher', 'teacher.id', '=', 'users.id')
                ->join('users as u1', 'u1.id', '=', 'teacher.created_by')
                ->join('avaliacao_alunos', 'avaliacao_alunos.created_by', '=', 'teacher.id')
                ->join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                ->join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                ->join('study_plans', 'study_plans.id', '=', 'study_plan_editions.study_plans_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'study_plans.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'plano_estudo_avaliacaos.disciplines_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->join('users_departments', 'users_departments.user_id', '=', 'teacher.id')
                ->leftJoin('department_translations as dpt', function ($join) {
                    $join->on('dpt.departments_id', '=', 'users_departments.departments_id');
                    $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dpt.active', '=', DB::raw(true));
                })
                ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                ->join('classes', 'classes.id', '=', 'avaliacao_aluno_historicos.class_id')
                ->select([
                          'teacher.name as name',
                          'ct.display_name as course_name',
                          'dt.display_name as discipline_name',
                          'classes.display_name as class_name',
                          'dpt.display_name as departments_name',

                        ])
                ->distinct('avaliacao_aluno_historicos.class_id');

            return DataTables::eloquent($model)
                    /*->addColumn('actions', function ($item) {
                            return view('Avaliations::avaliacao.datatables.actions')->with('item', $item);
                        })
                        ->rawColumns(['actions'])*/
                        ->addIndexColumn()
                        ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    public function searchByCourse($id)
    {
        try {
            $model = User::query()
                        ->join('users as teacher', 'teacher.id', '=', 'users.id')
                        ->join('users as u1', 'u1.id', '=', 'teacher.created_by')
                        ->join('avaliacao_alunos', 'avaliacao_alunos.created_by', '=', 'teacher.id')
                        ->join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                        ->join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                        ->join('study_plans', 'study_plans.id', '=', 'study_plan_editions.study_plans_id')
                        ->join('courses', 'courses.id', '=', 'study_plans.courses_id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'courses.id');
                            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', DB::raw(true));
                        })
                        ->leftJoin('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'plano_estudo_avaliacaos.disciplines_id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                        })
                        ->join('users_departments', 'users_departments.user_id', '=', 'teacher.id')
                        ->leftJoin('department_translations as dpt', function ($join) {
                            $join->on('dpt.departments_id', '=', 'users_departments.departments_id');
                            $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dpt.active', '=', DB::raw(true));
                        })
                        ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                        ->join('classes', 'classes.id', '=', 'avaliacao_aluno_historicos.class_id')
                        ->where('courses.id', $id)
                        ->select([
                                'teacher.name as name',
                                'ct.display_name as course_name',
                                'dt.display_name as discipline_name',
                                'classes.display_name as class_name',
                                'dpt.display_name as departments_name',

                                ])
                        ->distinct('avaliacao_aluno_historicos.class_id');

            return DataTables::eloquent($model)
                            /*->addColumn('actions', function ($item) {
                                    return view('Avaliations::avaliacao.datatables.actions')->with('item', $item);
                                })
                                ->rawColumns(['actions'])*/
                                ->addIndexColumn()
                                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function searchByDepartments($id)
    {
        try {
            $model = User::query()
                        ->join('users as teacher', 'teacher.id', '=', 'users.id')
                        ->join('users as u1', 'u1.id', '=', 'teacher.created_by')
                        ->join('avaliacao_alunos', 'avaliacao_alunos.created_by', '=', 'teacher.id')
                        ->join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                        ->join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                        ->join('study_plans', 'study_plans.id', '=', 'study_plan_editions.study_plans_id')
                        ->join('courses', 'courses.id', '=', 'study_plans.courses_id')
                        ->leftJoin('courses_translations as ct', function ($join) {
                            $join->on('ct.courses_id', '=', 'courses.id');
                            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('ct.active', '=', DB::raw(true));
                        })
                        ->leftJoin('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'plano_estudo_avaliacaos.disciplines_id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                        })
                        ->join('users_departments', 'users_departments.user_id', '=', 'teacher.id')
                        ->join('departments', 'departments.id', '=', 'users_departments.departments_id')
                        ->leftJoin('department_translations as dpt', function ($join) {
                            $join->on('dpt.departments_id', '=', 'departments.id');
                            $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dpt.active', '=', DB::raw(true));
                        })
                        ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                        ->join('classes', 'classes.id', '=', 'avaliacao_aluno_historicos.class_id')
                        ->where('departments.id', $id)
                        ->select([
                                'teacher.name as name',
                                'ct.display_name as course_name',
                                'dt.display_name as discipline_name',
                                'classes.display_name as class_name',
                                'dpt.display_name as departments_name',

                                ])
                        ->distinct('avaliacao_aluno_historicos.class_id');

            return DataTables::eloquent($model)
                            /*->addColumn('actions', function ($item) {
                                    return view('Avaliations::avaliacao.datatables.actions')->with('item', $item);
                                })
                                ->rawColumns(['actions'])*/
                                ->addIndexColumn()
                                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
}
