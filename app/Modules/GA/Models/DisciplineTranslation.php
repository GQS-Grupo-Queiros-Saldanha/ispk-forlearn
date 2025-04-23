<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplineTranslation
 *
 * @property int $id
 * @property int $discipline_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Discipline $discipline
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation newQuery()
 * @method static Builder|DisciplineTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineTranslation whereVersion($value)
 * @method static Builder|DisciplineTranslation withTrashed()
 * @method static Builder|DisciplineTranslation withoutTrashed()
 * @mixin Eloquent
 */
class DisciplineTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'disciplines_translations';

    protected $fillable = [
        'discipline_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'abbreviation',
        'objectives',
        'learning_outcomes',
        'topics',
        'bibliography',
        'teaching_methods',
        'assessment_strategy',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
