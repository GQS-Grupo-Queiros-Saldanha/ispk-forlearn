<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ParameterGroupTranslation
 *
 * @property int $id
 * @property int $parameter_group_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Language $language
 * @property-read ParameterGroup $parameter_group
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation newQuery()
 * @method static Builder|ParameterGroupTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereParameterGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation whereVersion($value)
 * @method static Builder|ParameterGroupTranslation withTrashed()
 * @method static Builder|ParameterGroupTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|ParameterGroupTranslation withCacheCooldownSeconds($seconds = null)
 */
class ParameterGroupTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'parameter_group_id',
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

    public function parameter_group()
    {
        return $this->belongsTo(ParameterGroup::class, 'parameter_group_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
