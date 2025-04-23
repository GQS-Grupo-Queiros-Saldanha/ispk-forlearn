<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\OptionalGroupTranslation
 *
 * @property int $id
 * @property int $optional_groups_id
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
 * @property-read DisciplineRegime $optionalGroup
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation newQuery()
 * @method static Builder|OptionalGroupTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereOptionalGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroupTranslation whereVersion($value)
 * @method static Builder|OptionalGroupTranslation withTrashed()
 * @method static Builder|OptionalGroupTranslation withoutTrashed()
 * @mixin Eloquent
 */
class OptionalGroupTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'optional_groups_translations';

    protected $fillable = [
        'optional_groups_id',
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

    public function optionalGroup()
    {
        return $this->belongsTo(DisciplineRegime::class, 'optional_groups_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
