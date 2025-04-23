<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ParameterOptionTranslation
 *
 * @property-read Language $language
 * @property-read ParameterOption $parameterOption
 * @method static bool|null forceDelete()
 * @method static Builder|ParameterOptionTranslation newModelQuery()
 * @method static Builder|ParameterOptionTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|ParameterOptionTranslation onlyTrashed()
 * @method static Builder|ParameterOptionTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ParameterOptionTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ParameterOptionTranslation withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $parameter_options_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|ParameterOptionTranslation whereActive($value)
 * @method static Builder|ParameterOptionTranslation whereCreatedAt($value)
 * @method static Builder|ParameterOptionTranslation whereDeletedAt($value)
 * @method static Builder|ParameterOptionTranslation whereDescription($value)
 * @method static Builder|ParameterOptionTranslation whereDisplayName($value)
 * @method static Builder|ParameterOptionTranslation whereId($value)
 * @method static Builder|ParameterOptionTranslation whereLanguageId($value)
 * @method static Builder|ParameterOptionTranslation whereParameterOptionsId($value)
 * @method static Builder|ParameterOptionTranslation whereUpdatedAt($value)
 * @method static Builder|ParameterOptionTranslation whereVersion($value)
 * @method static Builder|ParameterOptionTranslation disableCache()
 * @method static Builder|ParameterOptionTranslation withCacheCooldownSeconds($seconds = null)
 */
class ParameterOptionTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'parameter_options_id',
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

    public function parameterOption()
    {
        return $this->belongsTo(ParameterOption::class, 'parameter_options_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
