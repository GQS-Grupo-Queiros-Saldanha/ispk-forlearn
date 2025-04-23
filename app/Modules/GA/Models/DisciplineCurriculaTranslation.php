<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Modules\GA\Models\DisciplineCurriculaTranslation
 *
 * @property int $id
 * @property int $discipline_curricula_id
 * @property int $language_id
 * @property int|null $version
 * @property int|null $active
 * @property string|null $presentation
 * @property string|null $bibliography
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DisciplineCurricula $disciplineCurricula
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation newQuery()
 * @method static Builder|DisciplineCurriculaTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereBibliography($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereDisciplineCurriculaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation wherePresentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation whereVersion($value)
 * @method static Builder|DisciplineCurriculaTranslation withTrashed()
 * @method static Builder|DisciplineCurriculaTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurriculaTranslation withCacheCooldownSeconds($seconds = null)
 */
class DisciplineCurriculaTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'discipline_curricula_translations';

    protected $fillable = [
        'discipline_curricula_id',
        'language_id',
        'presentation',
        'bibliography',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function disciplineCurricula()
    {
        return $this->belongsTo(DisciplineCurricula::class, 'discipline_curricula_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
