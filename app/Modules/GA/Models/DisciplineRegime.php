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
 * App\Modules\GA\Models\DisciplineRegime
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
 * @property-read DisciplineRegimeTranslation $translation
 * @property-read Collection|DisciplineRegimeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime newQuery()
 * @method static Builder|DisciplineRegime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegime whereUpdatedBy($value)
 * @method static Builder|DisciplineRegime withTrashed()
 * @method static Builder|DisciplineRegime withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|StudyPlanHasDisciplineRegime[] $study_plans_has_discipline_regimes
 * @property-read int|null $study_plans_has_discipline_regimes_count
 * @property-read int|null $translations_count
 */
class DisciplineRegime extends Model
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
        return $this->hasMany(DisciplineRegimeTranslation::class, 'discipline_regimes_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DisciplineRegimeTranslation::class, 'discipline_regimes_id', 'id');
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

    public function study_plans_has_discipline_regimes()
    {
        return $this->hasMany(StudyPlanHasDisciplineRegime::class, 'discipline_regimes_id', 'id');
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
