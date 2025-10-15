<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplineRegimeTranslation
 *
 * @property int $id
 * @property int $discipline_regimes_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read DisciplineRegime $disciplineRegime
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation newQuery()
 * @method static Builder|DisciplineRegimeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereDisciplineRegimesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineRegimeTranslation whereVersion($value)
 * @method static Builder|DisciplineRegimeTranslation withTrashed()
 * @method static Builder|DisciplineRegimeTranslation withoutTrashed()
 * @mixin Eloquent
 */
class DisciplineRegimeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'discipline_regime_translations';

    protected $fillable = [
        'discipline_regimes_id',
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
    // Creators
    //================================================================================

    public function disciplineRegime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regimes_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
