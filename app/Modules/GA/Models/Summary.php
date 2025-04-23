<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Modules\GA\Models\Summary
 *
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read StudyPlanEditionDiscipline $discipline
 * @property-read Event $event
 * @property-read StudyPlanEditionDisciplineModule $module
 * @property-read DisciplineRegime $regime
 * @property-read User $updatedBy
 * @property-read User $user
 * @method static Builder|Summary newModelQuery()
 * @method static Builder|Summary newQuery()
 * @method static Builder|Summary query()
 * @mixin Eloquent
 * @property int $id
 * @property int $study_plan_edition_id
 * @property int $spe_discipline_id
 * @property int $module_id
 * @property int $event_id
 * @property int $discipline_regime_id
 * @property int $user_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read StudyPlanEdition $studyPlanEdition
 * @property-read LectiveYearTranslation $translation
 * @property-read Collection|LectiveYearTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|Summary whereCreatedAt($value)
 * @method static Builder|Summary whereCreatedBy($value)
 * @method static Builder|Summary whereDeletedAt($value)
 * @method static Builder|Summary whereDeletedBy($value)
 * @method static Builder|Summary whereDisciplineRegimeId($value)
 * @method static Builder|Summary whereEventId($value)
 * @method static Builder|Summary whereId($value)
 * @method static Builder|Summary whereModuleId($value)
 * @method static Builder|Summary whereSpeDisciplineId($value)
 * @method static Builder|Summary whereStudyPlanEditionId($value)
 * @method static Builder|Summary whereUpdatedAt($value)
 * @method static Builder|Summary whereUpdatedBy($value)
 * @method static Builder|Summary whereUserId($value)
 * @property int $study_plan_id
 * @property int $discipline_id
 * @property int $order
 * @property string $content
 * @property-read \App\Modules\GA\Models\StudyPlan $studyPlan
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Summary whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Summary whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Summary whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Summary whereStudyPlanId($value)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\GA\Models\Summary onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\GA\Models\Summary withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\GA\Models\Summary withoutTrashed()
 */
class Summary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'study_plan_id',
        'discipline_id',
        'discipline_regime_id',
        'order',
        'content',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(SummaryTranslation::class, 'summaries_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(SummaryTranslation::class, 'summaries_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Creators
    //================================================================================

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    //================================================================================
    // Relations
    //================================================================================

    public function studyPlan()
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regime_id', 'id', 'discipline_regimes');
    }

   /*public static function boot()
   {
       parent::boot();
        static::saving(function($model)
        {
            $user = Auth::user();
            //$model->created_by = $user->id;
            $model->updated_by = $user->id;
        });
        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });
   }*/

}
