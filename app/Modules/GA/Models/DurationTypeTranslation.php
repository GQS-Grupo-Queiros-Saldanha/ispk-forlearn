<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DurationTypeTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $duration_types_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DurationType $duration_type
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation newQuery()
 * @method static Builder|DurationTypeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereDurationTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation whereVersion($value)
 * @method static Builder|DurationTypeTranslation withTrashed()
 * @method static Builder|DurationTypeTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationTypeTranslation withCacheCooldownSeconds($seconds = null)
 */
class DurationTypeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'duration_type_translations';

    protected $fillable = [
        'duration_types_id',
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

    public function duration_type()
    {
        return $this->belongsTo(DurationType::class, 'duration_types_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
