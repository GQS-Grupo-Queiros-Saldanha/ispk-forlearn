<?php

namespace App\Modules\Reports\Util;


use App\Helpers\LanguageHelper;
use App\Modules\Payments\Models\ArticleRequest;
use Exception;
use Log;
use Illuminate\Support\Facades\DB;

class DocsReportsUtil
{

    public static function getArticleRequestStudents($date1, $date2, $courses, $classes, $articles, $rules, $students, $months)
    {
        try {

            if (is_null($date2))
                return self::getPendingArticleRequests($date1, $courses, $classes, $articles, $rules, $students);
            else
                return self::getTransactionArticleRequests($date1, $date2, $months, $courses, $classes, $articles, $rules, $students);
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    private static function getPendingArticleRequests($date1, $courses, $classes, $articles, $rules, $students)
    {

        $protocoloStudents = DocsReportsUtil::getPendingArticleRequestsByType($date1, $courses, $classes, $articles, $rules, $students, "protocolo");
        $normaltudents = DocsReportsUtil::getPendingArticleRequestsByType($date1, $courses, $classes, $articles, $rules, $students, "normal");

        $emoluments = $protocoloStudents->concat($normaltudents);

        $emoluments = collect($emoluments)->filter(function ($item) use ($emoluments) {
            $min_value = true;
            $ar = $item->id_article_requests;

            if (isset($item->rule_value)) {
                $ruleValue = $item->rule_value;
                $similar = $emoluments->where('id_article_requests', $ar)->where('rule_value', '!=', $ruleValue);
                if (!$similar->isEmpty()) {
                    $similar = $similar->first();
                    $min_value = $item->rule_value < $similar->rule_value ? true : false;
                }
            }

            return $min_value;
        });

        $emoluments = $emoluments->groupBy(['matriculation_number', 'course_name', 'user_name']);

        return $emoluments;
    }

    private static function getTransactionArticleRequests($date1, $date2, $month, $courses, $classes, $articles, $rules, $students)
    {

        $protocoloStudents = self::getTransactionArticleRequestsByType($date1, $date2, $month, $courses, $classes, $articles, $rules, $students, "protocolo");
        $normaltudents = self::getTransactionArticleRequestsByType($date1, $date2, $month, $courses, $classes, $articles, $rules, $students, "normal");

        $emoluments = $protocoloStudents->concat($normaltudents);

        $emoluments = collect($emoluments)->filter(function ($item) use ($emoluments) {
            $min_value = true;
            $ar = $item->id_article_requests;

            if (isset($item->rule_value)) {
                $ruleValue = $item->rule_value;
                $similar = $emoluments->where('id_article_requests', $ar)->where('rule_value', '!=', $ruleValue);
                if (!$similar->isEmpty()) {
                    $similar = $similar->first();
                    $min_value = $item->rule_value < $similar->rule_value ? true : false;
                }
            }

            return $min_value;
        });

        $emoluments = $emoluments->groupBy(['matriculation_number', 'course_name', 'user_name', 'id_article_requests']);

        return $emoluments;
    }
    private static function getPendingArticleRequestsByType($date1, $courses, $classes, $articles, $rules, $students, $type)
    {


        try {
            $select = [
                'at.article_id as article_id',
                'at.display_name as article_name',
                'users.name as user_name',
                'users.id as user_id',
                'ct.display_name as course_name',
                'ct.courses_id as course_id',
                'up_meca.value as matriculation_number',
                'matriculations.course_year as course_year',
                'article_requests.created_at as created_at',
                'article_requests.base_value as value',
                'article_requests.id as id_article_requests'
            ];

            if (isset($rules))
                array_push($selec, 'rules.valor as rule_value');

            $model =  ArticleRequest::leftJoin('article_translations as at', function ($join) {
                $join->on('at.article_id', '=', 'article_requests.article_id');
                $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', \DB::raw(true));
            })

                ->when($rules != null, function ($q) use ($type) {
                    $q->when($type == "normal", function ($query) {
                        $query->join('artcles_rules as rules', function ($join) {
                            $join->on('rules.id_articles', 'article.id');
                            $join->on('matriculat.course_year', 'rules.ano_curricular');
                            $join->whereNull('rules.scholarship_entity_id');
                        });


                        $bolseiros = DB::table('scholarship_holder as sh')
                            ->where('sh.are_scholarship_holder', 1)
                            ->get();
                        if (!$bolseiros->isEmpty())
                            $bolseiros = $bolseiros->pluck('user_id')->toArray();

                        $query->whereNotIn('matriculat.user_id', $bolseiros);
                    });
                    $q->when($type == "protocolo", function ($query) {
                        $query->join('artcles_rules as rules', 'rules.id_articles', 'article.id');
                        $query->whereNotNull('rules.scholarship_entity_id');
                        $query->join('scholarship_holder as sh', function ($join) {
                            $join->on('sh.scholarship_entity_id', 'rules.scholarship_entity_id')
                                ->on('sh.user_id', 'matriculations.user_id')
                                ->where('sh.are_scholarship_holder', 1);
                        });
                    });
                })
                ->join('users', 'users.id', '=', 'article_requests.user_id')
                ->leftJoin('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->leftJoin('matriculation_classes as mac', 'mac.matriculation_id', 'matriculations.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                    $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', \DB::raw(true));
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->select($select)
                ->whereDate('article_requests.created_at', $date1)
                ->where('article_requests.status', 'pending')
                ->when($rules != null, function ($q) use ($rules) {
                    return $q->whereIn('rules.id', $rules);
                })
                ->when($articles != null, function ($q) use ($articles) {
                    return $q->whereIn('at.article_id', $articles);
                })->when($courses != null, function ($q) use ($courses) {
                    return $q->whereIn('ct.courses_id', $courses);
                })->when($students != null, function ($q) use ($students) {
                    return $q->whereIn('users.id', $students);
                })
                ->when($classes != null, function ($q) use ($classes) {
                    return $q->whereIn('mac.class_id', $classes);
                })
                ->get();

            return $model;
        } catch (Exception $e) {
            return $e;
        }
    }

    private static function getTransactionArticleRequestsByType($date1, $date2, $months, $courses, $classes, $articles, $rules, $students, $type)
    {

        try {
            $select = [
                'tran_info.transaction_id as transaction_id_info',
                'trans.id as transaction_id',
                'trans.type as type',
                'matriculat.id as id',
                'matriculat.course_year as year',
                'matriculat.lective_year as lective_year',
                'article_reques.id as id_article_requests',
                'at.display_name as article_name',
                'full_name.value as user_name',
                'ct.display_name as course_name',
                'up_meca.value as matriculation_number',
                'u1.name as created_by_user',
                'at.article_id as article_id',
                'u1.id as user_id',
                'dcp.display_name as discplina_display_name',
                'ct.courses_id as course_id',
                'article_reques.created_at as created_at',
                'article_reques.base_value as value',
                'article_reques.year as article_year',
                'article_reques.month as article_month',
                'article_reques.status as status',
                'trans_articl_reques.value as price',
                'article_reques.user_id as student',
            ];

            if (isset($rules))
                array_push($select, 'rules.valor as rule_value');

            $model = DB::table('transactions as trans')
                ->join('transaction_article_requests as trans_articl_reques', 'trans_articl_reques.transaction_id', '=', 'trans.id')
                ->join('article_requests as article_reques', 'article_reques.id', '=', 'trans_articl_reques.article_request_id')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'article_reques.article_id');
                    $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', \DB::raw(true));
                })
                ->leftJoin('articles as article', 'article.id', '=', 'at.article_id')

                ->join('users as us', 'us.id', '=', 'article_reques.user_id')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('us.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('us.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->join('user_courses as uc', 'uc.users_id', '=', 'us.id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', \DB::raw(true));
                })
                ->leftJoin('users as u1', 'u1.id', '=', 'trans.created_by')
                ->leftJoin('user_parameters as user_va', function ($join) {
                    $join->on('u1.id', '=', 'user_va.users_id')
                        ->where('user_va.parameters_id', 1);
                })
                ->leftJoin('transaction_info as tran_info', function ($join) {
                    $join->on('trans.id', '=', 'tran_info.transaction_id');
                })
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'article_reques.discipline_id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->leftJoin('matriculations as matriculat', function ($join) {
                    $join->on('matriculat.user_id', '=', 'article_reques.user_id');
                })
                ->leftJoin('matriculation_classes as mac', 'mac.matriculation_id', 'matriculat.id')
                ->when($rules != null, function ($q) use ($type) {
                    $q->when($type == "normal", function ($query) {
                        $query->join('artcles_rules as rules', function ($join) {
                            $join->on('rules.id_articles', 'article.id');
                            $join->on('matriculat.course_year', 'rules.ano_curricular');
                            $join->whereNull('rules.scholarship_entity_id');
                        });


                        $bolseiros = DB::table('scholarship_holder as sh')
                            ->where('sh.are_scholarship_holder', 1)
                            ->get();
                        if (!$bolseiros->isEmpty())
                            $bolseiros = $bolseiros->pluck('user_id')->toArray();

                        $query->whereNotIn('matriculat.user_id', $bolseiros);
                    });
                    $q->when($type == "protocolo", function ($query) {
                        $query->join('artcles_rules as rules', 'rules.id_articles', 'article.id');
                        $query->whereNotNull('rules.scholarship_entity_id');
                        $query->join('scholarship_holder as sh', function ($join) {
                            $join->on('sh.scholarship_entity_id', 'rules.scholarship_entity_id')
                                ->on('sh.user_id', 'matriculat.user_id')
                                ->where('sh.are_scholarship_holder', 1);
                        });
                    });
                })

                ->select($select)
                ->whereBetween('matriculat.created_at', [$date1, $date2])
                ->whereNull('article_reques.deleted_at')
                ->whereNull('article_reques.deleted_by')
                ->whereNull('trans.deleted_at')
                ->distinct('article_reques.id')
                ->where('trans.data_from', '!=', 'Estorno')
                ->where('article_reques.status', '!=', 'total')
                ->when($rules != null, function ($q) use ($rules) {
                    return $q->whereIn('rules.id', $rules);
                })
                ->when($students != null, function ($q) use ($students) {
                    return $q->whereIn('us.id', $students);
                })
                ->when($classes != null, function ($q) use ($classes) {
                    return $q->whereIn('mac.class_id', $classes);
                })
                ->when($courses != null, function ($q) use ($courses) {
                    return $q->whereIn('ct.courses_id', $courses);
                })
                ->when($articles != null, function ($q) use ($articles) {
                    return $q->whereIn('at.article_id', $articles);
                })
                ->when(count($months) == 1  && in_array('3_2020', $months) && $months != 0, function ($q) use ($months) {
                    $q->where('article_reques.year', '=', "2020");
                    return $q->whereIn('article_reques.month', $months);
                })
                ->when(count($months) > 0  && in_array('3_2020', $months) && in_array('3', $months) && $months != 0, function ($q) use ($months) {
                    return $q->whereIn('article_reques.month', $months);
                })
                ->when(count($months) > 0  && in_array('3_2020', $months) == false && in_array('3', $months) == false && $months != 0, function ($q) use ($months) {
                    return $q->whereIn('article_reques.month', $months);
                })
                ->when(count($months) > 0  && in_array('3', $months) == true && $months != 0, function ($q) use ($months) {
                    $q->where('article_reques.year', '>', "2020");
                    return $q->whereIn('article_reques.month', $months);
                })
                ->when(count($months) > 0  && in_array('3', $months) == false && $months != 0, function ($q) use ($months) {
                    return $q->whereIn('article_reques.month', $months);
                })
                ->orderBy('article_reques.year', 'ASC')
                ->orderBy('article_reques.month', 'ASC')
                ->orderBy('id_article_requests', 'asc')
                ->get();


            return $model;
        } catch (Exception $e) {
            return $e;
        }
    }
}
