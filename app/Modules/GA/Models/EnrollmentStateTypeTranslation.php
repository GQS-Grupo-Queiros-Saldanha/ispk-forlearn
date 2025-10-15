<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\EnrollmentStateTypeTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $enrollment_state_types_id
 * @property string|null $display_name
 * @property string|null $description
 * @property string|null $abbreviation
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read EnrollmentStateType $enrollmentStateType
 * @property-read Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation disableCache()
 * @method static bool|null forceDelete()
 * @method static CachedBuilder|EnrollmentStateTypeTranslation newModelQuery()
 * @method static CachedBuilder|EnrollmentStateTypeTranslation newQuery()
 * @method static Builder|EnrollmentStateTypeTranslation onlyTrashed()
 * @method static CachedBuilder|EnrollmentStateTypeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereEnrollmentStateTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateTypeTranslation withCacheCooldownSeconds($seconds = null)
 * @method static Builder|EnrollmentStateTypeTranslation withTrashed()
 * @method static Builder|EnrollmentStateTypeTranslation withoutTrashed()
 * @mixin Eloquent
 */
class EnrollmentStateTypeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'enrollment_state_type_translations';

    protected $fillable = [
        'enrollment_state_types_id',
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

    public function enrollmentStateType()
    {
        return $this->belongsTo(EnrollmentStateType::class, 'enrollment_state_types_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
