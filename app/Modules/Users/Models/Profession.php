<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\Profession
 *
 * @property-read Collection|ProfessionTranslation[] $translations
 * @method static bool|null forceDelete()
 * @method static Builder|Profession newModelQuery()
 * @method static Builder|Profession newQuery()
 * @method static \Illuminate\Database\Query\Builder|Profession onlyTrashed()
 * @method static Builder|Profession query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|Profession withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Profession withoutTrashed()
 * @mixin Eloquent
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
 * @property-read User|null $updatedBy
 * @method static Builder|Profession whereCode($value)
 * @method static Builder|Profession whereCreatedAt($value)
 * @method static Builder|Profession whereCreatedBy($value)
 * @method static Builder|Profession whereDeletedAt($value)
 * @method static Builder|Profession whereDeletedBy($value)
 * @method static Builder|Profession whereId($value)
 * @method static Builder|Profession whereUpdatedAt($value)
 * @method static Builder|Profession whereUpdatedBy($value)
 * @property-read int|null $translations_count
 */
class Profession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
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
        return $this->hasMany(ProfessionTranslation::class, 'professions_id', 'id');
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
