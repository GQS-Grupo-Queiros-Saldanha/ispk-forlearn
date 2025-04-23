<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplineProfileTranslation
 *
 * @property int $id
 * @property int $discipline_profiles_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read DisciplineProfile $disciplineProfile
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation newQuery()
 * @method static Builder|DisciplineProfileTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereDisciplineProfilesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfileTranslation whereVersion($value)
 * @method static Builder|DisciplineProfileTranslation withTrashed()
 * @method static Builder|DisciplineProfileTranslation withoutTrashed()
 * @mixin Eloquent
 */
class DisciplineProfileTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'discipline_profile_translations';

    protected $fillable = [
        'discipline_profiles_id',
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

    public function disciplineProfile()
    {
        return $this->belongsTo(DisciplineProfile::class, 'discipline_profiles_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
