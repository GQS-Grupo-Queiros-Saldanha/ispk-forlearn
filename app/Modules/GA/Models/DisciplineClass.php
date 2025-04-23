<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\DisciplineClass
 *
 * @property int $id
 * @property int $classes_id
 * @property string $code
 * @property string $display_name
 * @property int $disciplines_id
 * @property int $study_plan_editions_id
 * @property int $discipline_regimes_id
 * @property-read Classes $classes
 * @property-read Discipline $discipline
 * @property-read DisciplineRegime $discipline_regime
 * @property-read StudyPlanEdition $study_plan_edition
 * @method static Builder|DisciplineClass newModelQuery()
 * @method static Builder|DisciplineClass newQuery()
 * @method static Builder|DisciplineClass query()
 * @method static Builder|DisciplineClass whereClassesId($value)
 * @method static Builder|DisciplineClass whereCode($value)
 * @method static Builder|DisciplineClass whereDisciplineRegimesId($value)
 * @method static Builder|DisciplineClass whereDisciplinesId($value)
 * @method static Builder|DisciplineClass whereDisplayName($value)
 * @method static Builder|DisciplineClass whereId($value)
 * @method static Builder|DisciplineClass whereStudyPlanEditionsId($value)
 * @mixin Eloquent
 */
class DisciplineClass extends Model
{

    public $timestamps = false;

    protected $table = 'discipline_classes';

    protected $fillable = [
        'classes_id',
        'disciplines_id',
        'study_plan_editions_id',
        'discipline_regimes_id',
        'display_name'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'classes_id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id');
    }

    public function discipline_regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regimes_id');
    }

    public function study_plan_edition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id');
    }
}
