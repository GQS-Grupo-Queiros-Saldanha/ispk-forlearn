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
 * App\Modules\GA\Models\LectiveYear
 *
 * @property int $id
 * @property string|null $code
 * @property string $start_date
 * @property string $end_date
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read LectiveYearTranslation $translation
 * @property-read Collection|LectiveYearTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear newQuery()
 * @method static Builder|LectiveYear onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LectiveYear whereUpdatedBy($value)
 * @method static Builder|LectiveYear withTrashed()
 * @method static Builder|LectiveYear withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class LectiveYear extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'start_date',
        'end_date',
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
        return $this->hasMany(LectiveYearTranslation::class, 'lective_years_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(LectiveYearTranslation::class, 'lective_years_id', 'id');
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
}
