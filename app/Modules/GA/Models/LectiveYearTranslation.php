<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\LectiveYearTranslation
 *
 * @property int $id
 * @property int $lective_years_id
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
 * @property-read LectiveYear $lectiveYear
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation newQuery()
 * @method static Builder|LectiveYearTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereLectiveYearsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYearTranslation whereVersion($value)
 * @method static Builder|LectiveYearTranslation withTrashed()
 * @method static Builder|LectiveYearTranslation withoutTrashed()
 * @mixin Eloquent
 */
class LectiveYearTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'lective_year_translations';

    protected $fillable = [
        'lective_years_id',
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

    public function lectiveYear()
    {
        return $this->belongsTo(LectiveYear::class, 'lective_years_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
