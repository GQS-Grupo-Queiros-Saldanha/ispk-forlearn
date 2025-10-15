<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\YearTransitionRuleTranslation
 *
 * @property int $id
 * @property int $ytr_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Language $language
 * @property-read YearTransitionRule $yearTransitionRule
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation newQuery()
 * @method static Builder|YearTransitionRuleTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRuleTranslation whereYtrId($value)
 * @method static Builder|YearTransitionRuleTranslation withTrashed()
 * @method static Builder|YearTransitionRuleTranslation withoutTrashed()
 * @mixin Eloquent
 */
class YearTransitionRuleTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'year_transition_rule_translations';

    protected $fillable = [
        'ytr_id',
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

    public function yearTransitionRule()
    {
        return $this->belongsTo(YearTransitionRule::class, 'ytr_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
