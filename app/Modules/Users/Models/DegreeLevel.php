<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\DegreeLevel
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
 * @property-read Collection|DegreeLevelTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel newQuery()
 * @method static Builder|DegreeLevel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DegreeLevel whereUpdatedBy($value)
 * @method static Builder|DegreeLevel withTrashed()
 * @method static Builder|DegreeLevel withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class DegreeLevel extends Model
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
        return $this->hasMany(DegreeLevelTranslation::class, 'degree_levels_id', 'id');
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
