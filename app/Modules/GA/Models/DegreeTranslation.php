<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DegreeTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $degrees_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Degree $degree
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation newQuery()
 * @method static Builder|DegreeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereDegreesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation whereVersion($value)
 * @method static Builder|DegreeTranslation withTrashed()
 * @method static Builder|DegreeTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeTranslation withCacheCooldownSeconds($seconds = null)
 */
class DegreeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'degree_translations';

    protected $fillable = [
        'degrees_id',
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

    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degrees_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
