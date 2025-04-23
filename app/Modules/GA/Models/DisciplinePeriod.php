<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplinePeriod
 *
 * @property int $id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read DisciplinePeriodTranslation $translation
 * @property-read Collection|DisciplinePeriodTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod newQuery()
 * @method static Builder|DisciplinePeriod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriod whereUpdatedBy($value)
 * @method static Builder|DisciplinePeriod withTrashed()
 * @method static Builder|DisciplinePeriod withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|PeriodTypeTranslation[] $periodTypes
 * @property-read Collection|StudyPlanHasDiscipline[] $study_plans_has_disciplines
 * @property-read Collection|StudyPlanHasOptionalGroup[] $study_plans_has_optional_groups
 * @property-read int|null $period_types_count
 * @property-read int|null $study_plans_has_disciplines_count
 * @property-read int|null $study_plans_has_optional_groups_count
 * @property-read int|null $translations_count
 */
class DisciplinePeriod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
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
        return $this->hasMany(DisciplinePeriodTranslation::class, 'discipline_periods_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DisciplinePeriodTranslation::class, 'discipline_periods_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Relations
    //================================================================================

    public function periodTypes()
    {
        return $this->hasMany(PeriodType::class, 'discipline_periods_id', 'id');
    }

    public function study_plans_has_optional_groups()
    {
        return $this->hasMany(StudyPlanHasOptionalGroup::class, 'discipline_periods', 'id');
    }

    public function study_plans_has_disciplines()
    {
        return $this->hasMany(StudyPlanHasDiscipline::class, 'discipline_periods', 'id');
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
}
