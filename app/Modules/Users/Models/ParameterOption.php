<?php

namespace App\Modules\Users\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ParameterOption
 *
 * @property-read Parameter $parameter
 * @property-read Collection|ParameterOptionTranslation[] $translations
 * @method static bool|null forceDelete()
 * @method static Builder|ParameterOption newModelQuery()
 * @method static Builder|ParameterOption newQuery()
 * @method static \Illuminate\Database\Query\Builder|ParameterOption onlyTrashed()
 * @method static Builder|ParameterOption query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ParameterOption withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ParameterOption withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $parameters_id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 * @method static Builder|ParameterOption whereCode($value)
 * @method static Builder|ParameterOption whereCreatedAt($value)
 * @method static Builder|ParameterOption whereCreatedBy($value)
 * @method static Builder|ParameterOption whereDeletedAt($value)
 * @method static Builder|ParameterOption whereDeletedBy($value)
 * @method static Builder|ParameterOption whereId($value)
 * @method static Builder|ParameterOption whereParametersId($value)
 * @method static Builder|ParameterOption whereUpdatedAt($value)
 * @method static Builder|ParameterOption whereUpdatedBy($value)
 * @property-read ParameterOptionTranslation $translation
 * @property int $has_related_parameters
 * @property-read Collection|Parameter[] $relatedParameters
 * @method static Builder|ParameterOption whereHasRelatedParameters($value)
 * @property-read int|null $related_parameters_count
 * @property-read int|null $translations_count
 */
class ParameterOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parameters_id',
        'code',
        'has_related_parameters',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(ParameterOptionTranslation::class, 'parameter_options_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(ParameterOptionTranslation::class, 'parameter_options_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Creators
    //================================================================================

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function relatedParameters()
    {
        return $this->belongsToMany(Parameter::class, 'parameter_option_has_parameters', 'parameter_option_id', 'parameters_id')->withPivot(['order'])->orderBy('parameter_option_has_parameters.order');
    }

    public function relatedParametersRecursive()
    {
        return $this->relatedParameters()->with([
            'currentTranslation',
            'roles',
            'options' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'relatedParametersRecursive'
                ]);
            }
        ]);
    }

    //================================================================================
    // Relations
    //================================================================================

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameters_id');
    }
}
