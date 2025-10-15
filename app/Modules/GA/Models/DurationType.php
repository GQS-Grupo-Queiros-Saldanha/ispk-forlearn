<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DurationType
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Course[] $courses
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read DurationTypeTranslation $translation
 * @property-read Collection|DurationTypeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType newQuery()
 * @method static Builder|DurationType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DurationType whereUpdatedBy($value)
 * @method static Builder|DurationType withTrashed()
 * @method static Builder|DurationType withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $courses_count
 * @property-read int|null $translations_count
 */
class DurationType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(DurationTypeTranslation::class, 'duration_types_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DurationTypeTranslation::class, 'duration_types_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Relations
    //================================================================================

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_cycles_id', 'id');
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
}
