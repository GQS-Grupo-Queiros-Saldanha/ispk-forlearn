<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Modules\GA\Models\PeriodTypeTranslation
 *
 * @property int $id
 * @property int $period_types_id
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
 * @property-read PeriodType $periodType
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation disableCache()
 * @method static bool|null forceDelete()
 * @method static CachedBuilder|PeriodTypeTranslation newModelQuery()
 * @method static CachedBuilder|PeriodTypeTranslation newQuery()
 * @method static Builder|PeriodTypeTranslation onlyTrashed()
 * @method static CachedBuilder|PeriodTypeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation wherePeriodTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PeriodTypeTranslation withCacheCooldownSeconds($seconds = null)
 * @method static Builder|PeriodTypeTranslation withTrashed()
 * @method static Builder|PeriodTypeTranslation withoutTrashed()
 * @mixin Eloquent
 */
class PeriodTypeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'period_type_translations';

    protected $fillable = [
        'period_types_id',
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

    public function periodType()
    {
        return $this->belongsTo(PeriodType::class, 'period_types_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
