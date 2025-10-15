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
 * App\Modules\GA\Models\AverageCalculationRule
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
 * @property-read AverageCalculationRuleTranslation $translation
 * @property-read Collection|AverageCalculationRuleTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule newQuery()
 * @method static Builder|AverageCalculationRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRule whereUpdatedBy($value)
 * @method static Builder|AverageCalculationRule withTrashed()
 * @method static Builder|AverageCalculationRule withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class AverageCalculationRule extends Model
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
        return $this->hasMany(AverageCalculationRuleTranslation::class, 'acr_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(AverageCalculationRuleTranslation::class, 'acr_id', 'id');
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
}
