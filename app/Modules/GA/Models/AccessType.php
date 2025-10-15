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
 * App\Modules\GA\Models\AccessType
 *
 * @property int $id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read AccessTypeTranslation $translation
 * @property-read Collection|AccessTypeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType newQuery()
 * @method static Builder|AccessType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessType whereUpdatedBy($value)
 * @method static Builder|AccessType withTrashed()
 * @method static Builder|AccessType withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class AccessType extends Model
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
        return $this->hasMany(AccessTypeTranslation::class, 'access_type_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(AccessTypeTranslation::class, 'access_type_id', 'id');
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
