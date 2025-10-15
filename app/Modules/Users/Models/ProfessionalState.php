<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ProfessionalState
 *
 * @property-read Collection|ProfessionalStateTranslation[] $translations
 * @method static bool|null forceDelete()
 * @method static Builder|ProfessionalState newModelQuery()
 * @method static Builder|ProfessionalState newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalState onlyTrashed()
 * @method static Builder|ProfessionalState query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalState withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalState withoutTrashed()
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
 * @method static Builder|ProfessionalState whereCode($value)
 * @method static Builder|ProfessionalState whereCreatedAt($value)
 * @method static Builder|ProfessionalState whereCreatedBy($value)
 * @method static Builder|ProfessionalState whereDeletedAt($value)
 * @method static Builder|ProfessionalState whereDeletedBy($value)
 * @method static Builder|ProfessionalState whereId($value)
 * @method static Builder|ProfessionalState whereUpdatedAt($value)
 * @method static Builder|ProfessionalState whereUpdatedBy($value)
 * @property-read int|null $translations_count
 */
class ProfessionalState extends Model
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
        return $this->hasMany(ProfessionalStateTranslation::class, 'professional_states_id', 'id');
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
