<?php

use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\StudyPlanHasDiscipline;
use App\Modules\GA\Models\Summary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function disciplinesSelect($courses = [], $teacher = null)
{
    $disciplines = Discipline::with([
        'currentTranslation'
    ]);

    if ($teacher) {
        $disciplines = $disciplines->whereIn('id', $teacher->disciplines()->pluck('id')->all());
    }

    if (!empty($courses)) {
        $disciplines = $disciplines->whereIn('courses_id', $courses);
    }

    $disciplines = $disciplines->get();

    return $disciplines;
    /*return $disciplines->map(function ($discipline) {
        return [
            'id' => $discipline->id,
            'display_name' => "#$discipline->code - " . $discipline->translation
        ];
    });*/
}

function disciplinesSelectForCandidates($courses = [], $teacher = null)
{
    $disciplines = Discipline::with([
        'currentTranslation'
    ])->where('discipline_profiles_id', 8);
    // ])->whereIn('id', [680,645,654,655,656,658,659,660,661,662,663,665,666,667]);

    if ($teacher) {
        $disciplines = $disciplines->whereIn('id', $teacher->disciplines()->pluck('id')->all());
    }

    if (!empty($courses)) {
        $disciplines = $disciplines->whereIn('courses_id', $courses);
    }

    $disciplines = $disciplines->get();

    return $disciplines;
    /*return $disciplines->map(function ($discipline) {
        return[
            'id' => $discipline->id,
            'display_name' => "#$discipline->code - " . $discipline->translation
            ];
    });
    
    */
}

function classesSelectForCandidates($courses = [], $teacher = null)
{
    $classes = Classes::query();
    $currentData = Carbon::now();
    $lectiveYear = DB::table('lective_years')
        ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
        ->get();

    if ($teacher) {
        $classes = $classes->whereIn('id', $teacher->classes()->pluck('id')->all());
    }

    if (!empty($courses)) {
        $classes = $classes->whereIn('courses_id', $courses)
            ->where('year', 1)
            ->where('lective_year_id', $lectiveYear->first()->id);
    }

    $classes = $classes->get();

    return $classes->map(function ($class) {
        return [
            'id' => $class->id,
            'display_name' => $class->display_name
        ];
    });
}

function classesSelect($courses = [], $teacher = null)
{
    $classes = Classes::query();

    if ($teacher) {
        $classes = $classes->whereIn('id', $teacher->classes()->pluck('id')->all());
    }

    if (!empty($courses)) {
        $classes = $classes->whereIn('courses_id', $courses);
    }

    $classes = $classes->get();

    return $classes->map(function ($class) {
        return [
            'id' => $class->id,
            'display_name' => $class->display_name
        ];
    });
}


function summariesByDisciplineRegimeInfo($studyPlanId, $disciplineId, $regimeId)
{
    $summaries = Summary::where('study_plan_id', $studyPlanId)
        ->where('discipline_id', $disciplineId)
        ->where('discipline_regime_id', $regimeId)
        ->get();

    $studyPlanDiscipline = StudyPlanHasDiscipline::where('study_plans_id', $studyPlanId)
        ->where('disciplines_id', $disciplineId)
        ->with('study_plans_has_discipline_regimes')
        ->first();

    $regimes = $studyPlanDiscipline
        ->study_plans_has_discipline_regimes
        ->map(function ($regime) {
            return [
                'id' => $regime->discipline_regime->id,
                'total_hours' => $regime->hours
            ];
        });

    return compact('summaries', 'regimes');
}

function reorderSummaries(Summary $summary, $newOrder)
{
    $relatedSummaries = Summary::where('study_plan_id', $summary->study_plan_id)
        ->where('discipline_id', $summary->discipline_id)
        //->where('discipline_regime_id', $summary->discipline_regime_id)
        ->get();

    $currentOrder = $summary->order;

    if ($newOrder) {
        $minOrder = $relatedSummaries->min('order');
        if ($newOrder < $minOrder) {
            $newOrder = $minOrder;
        }

        $maxOrder = $relatedSummaries->max('order');
        if ($newOrder > $maxOrder) {
            $newOrder = $maxOrder;
        }

        if ($newOrder === $currentOrder) {
            return $summary;
        }

        $affectedSummaries = $relatedSummaries->filter(function (Summary $sum, $key) use ($newOrder, $currentOrder) {
            $affected = $newOrder > $currentOrder ?
                ($sum->order > $currentOrder && $sum->order <= $newOrder) : ($sum->order >= $newOrder && $sum->order < $currentOrder);
            return $sum->order !== $currentOrder && $affected;
        });

        $affectedSummaries->each(function (Summary $sum, $key) use ($newOrder, $currentOrder) {
            $sum->order += $newOrder > $currentOrder ? -1 : 1;
            $sum->save();
        });

        $summary->order = $newOrder;
    } else {
        // $newOrder is null when deleting the summary

        $affectedSummaries = $relatedSummaries->filter(function (Summary $sum, $key) use ($currentOrder) {
            return $sum->order !== $currentOrder && $sum->order > $currentOrder;
        });

        $affectedSummaries->each(function (Summary $sum, $key) use ($newOrder, $currentOrder) {
            $sum->order -= 1;
            $sum->save();
        });
    }

    return $summary;
}



function EmolumentCodeV($search, $lective_year_id)
{

    $Consulta = DB::table('emolument_codev_matriculation as emo')
        ->join('articles as art', 'art.id', '=', 'emo.id_article')
        ->join('code_developer as code', 'code.id', '=', 'emo.id_codev')
        ->select(['emo.descricao', 'art.id as id_emolumento', 'emo.lective_year_id as lectiveYear', 'code.code as codigo_dev'])
        ->where('code.code', $search)
        ->where('emo.lective_year_id', $lective_year_id)
        ->get();
    return $Consulta;
}


function EmolumentCodevLective($search, $lective_year_id)
{
    //dd($search, $lective_year_id->id);
    $Consulta = DB::table('articles as art')
        ->join('code_developer as code', 'code.id', '=', 'art.id_code_dev')
        ->select(['art.code', 'art.id as id_emolumento', 'art.anoLectivo as lectiveYear', 'code.code as codigo_dev'])
        ->where('code.code', $search)
        ->where('art.anoLectivo', $lective_year_id->id)
        ->get();
    return $Consulta;
}
