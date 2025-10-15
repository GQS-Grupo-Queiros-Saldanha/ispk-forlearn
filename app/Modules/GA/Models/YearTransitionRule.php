<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\YearTransitionRule
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
 * @property-read YearTransitionRuleTranslation $translation
 * @property-read Collection|YearTransitionRuleTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule newQuery()
 * @method static Builder|YearTransitionRule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YearTransitionRule whereUpdatedBy($value)
 * @method static Builder|YearTransitionRule withTrashed()
 * @method static Builder|YearTransitionRule withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class YearTransitionRule extends Model
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
        return $this->hasMany(YearTransitionRuleTranslation::class, 'ytr_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(YearTransitionRuleTranslation::class, 'ytr_id', 'id');
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
