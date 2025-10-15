<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\DegreeLevelTranslation
 *
 * @property int $id
 * @property int $degree_levels_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DegreeLevel $degreeLevel
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation newQuery()
 * @method static Builder|DegreeLevelTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereDegreeLevelsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation whereVersion($value)
 * @method static Builder|DegreeLevelTranslation withTrashed()
 * @method static Builder|DegreeLevelTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevelTranslation withCacheCooldownSeconds($seconds = null)
 */
class DegreeLevelTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'degree_level_translations';

    protected $fillable = [
        'degree_levels_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function degreeLevel()
    {
        return $this->belongsTo(DegreeLevel::class, 'degree_levels_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
