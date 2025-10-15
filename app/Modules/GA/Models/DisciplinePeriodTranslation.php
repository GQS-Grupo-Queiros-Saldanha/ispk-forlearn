<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplinePeriodTranslation
 *
 * @property int $id
 * @property int $discipline_periods_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read DisciplinePeriod $disciplinePeriod
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation newQuery()
 * @method static Builder|DisciplinePeriodTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereDisciplinePeriodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplinePeriodTranslation whereVersion($value)
 * @method static Builder|DisciplinePeriodTranslation withTrashed()
 * @method static Builder|DisciplinePeriodTranslation withoutTrashed()
 * @mixin Eloquent
 */
class DisciplinePeriodTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;
    protected $table = 'discipline_period_translations';

    protected $fillable = [
        'discipline_periods_id',
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

    public function disciplinePeriod()
    {
        return $this->belongsTo(DisciplinePeriod::class, 'discipline_periods_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
