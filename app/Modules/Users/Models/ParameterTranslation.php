<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ParameterTranslation
 *
 * @property-read Language $language
 * @property-read Parameter $parameter
 * @method static bool|null forceDelete()
 * @method static Builder|ParameterTranslation newModelQuery()
 * @method static Builder|ParameterTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|ParameterTranslation onlyTrashed()
 * @method static Builder|ParameterTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ParameterTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ParameterTranslation withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $parameters_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|ParameterTranslation whereActive($value)
 * @method static Builder|ParameterTranslation whereCreatedAt($value)
 * @method static Builder|ParameterTranslation whereDeletedAt($value)
 * @method static Builder|ParameterTranslation whereDescription($value)
 * @method static Builder|ParameterTranslation whereDisplayName($value)
 * @method static Builder|ParameterTranslation whereId($value)
 * @method static Builder|ParameterTranslation whereLanguageId($value)
 * @method static Builder|ParameterTranslation whereParametersId($value)
 * @method static Builder|ParameterTranslation whereUpdatedAt($value)
 * @method static Builder|ParameterTranslation whereVersion($value)
 * @method static Builder|ParameterTranslation disableCache()
 * @method static Builder|ParameterTranslation withCacheCooldownSeconds($seconds = null)
 */
class ParameterTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'parameters_id',
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

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameters_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
