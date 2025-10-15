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
 * App\Modules\GA\Models\PeriodType
 *
 * @property int $id
 * @property int $discipline_periods_id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read DisciplinePeriod $disciplinePeriod
 * @property-read PeriodTypeTranslation $translation
 * @property-read Collection|PeriodTypeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType newQuery()
 * @method static Builder|PeriodType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereDisciplinePeriodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodType whereUpdatedBy($value)
 * @method static Builder|PeriodType withTrashed()
 * @method static Builder|PeriodType withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class PeriodType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'discipline_periods_id',
        'code',
        'start_date',
        'end_date',
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
        return $this->hasMany(PeriodTypeTranslation::class, 'period_types_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(PeriodTypeTranslation::class, 'period_types_id', 'id');
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

    public function disciplinePeriod()
    {
        return $this->belongsTo(DisciplinePeriod::class, 'discipline_periods_id', 'id');
    }
}
