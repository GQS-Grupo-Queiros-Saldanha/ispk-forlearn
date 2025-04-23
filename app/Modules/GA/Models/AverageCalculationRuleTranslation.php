<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\AverageCalculationRuleTranslation
 *
 * @property int $id
 * @property int $acr_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read AverageCalculationRule $averageCalculationRule
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation newQuery()
 * @method static Builder|AverageCalculationRuleTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereAcrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AverageCalculationRuleTranslation whereVersion($value)
 * @method static Builder|AverageCalculationRuleTranslation withTrashed()
 * @method static Builder|AverageCalculationRuleTranslation withoutTrashed()
 * @mixin Eloquent
 */
class AverageCalculationRuleTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'average_calculation_rule_translations';

    protected $fillable = [
        'acr_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'abbreviation',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function averageCalculationRule()
    {
        return $this->belongsTo(AverageCalculationRule::class, 'acr_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
