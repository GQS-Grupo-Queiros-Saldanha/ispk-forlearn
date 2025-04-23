<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplineAreaTranslation
 *
 * @property int $id
 * @property int $discipline_areas_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read DisciplineArea $disciplineArea
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation newQuery()
 * @method static Builder|DisciplineAreaTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereDisciplineAreasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineAreaTranslation whereVersion($value)
 * @method static Builder|DisciplineAreaTranslation withTrashed()
 * @method static Builder|DisciplineAreaTranslation withoutTrashed()
 * @mixin Eloquent
 */
class DisciplineAreaTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'discipline_areas_translations';

    protected $fillable = [
        'discipline_areas_id',
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

    public function disciplineArea()
    {
        return $this->belongsTo(DisciplineArea::class, 'discipline_areas_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
