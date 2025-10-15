<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DepartmentTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $departments_id
 * @property string|null $abbreviation
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Department $department
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation newQuery()
 * @method static Builder|DepartmentTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereDepartmentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation whereVersion($value)
 * @method static Builder|DepartmentTranslation withTrashed()
 * @method static Builder|DepartmentTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentTranslation withCacheCooldownSeconds($seconds = null)
 */
class DepartmentTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'department_translations';

    protected $fillable = [
        'departments_id',
        'language_id',
        'display_name',
        'description',
        'abbreviation',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function department()
    {
        return $this->belongsTo(Department::class, 'departments_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
