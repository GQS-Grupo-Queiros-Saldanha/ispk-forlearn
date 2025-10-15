<?php

namespace App\Modules\Users\Models;

use App\Model;

use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Discipline;
use App\Modules\Payments\Models\ArticleRequest;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Modules\Users\Models\Matriculation
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property int $course_year
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Classes[] $classes
 * @property-read int|null $classes_count
 * @property-read \App\Modules\Users\Models\User $createdBy
 * @property-read \App\Modules\Users\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Discipline[] $disciplines
 * @property-read int|null $disciplines_count
 * @property-read \App\Modules\Users\Models\User|null $updatedBy
 * @property-read \App\Modules\Users\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Users\Models\Matriculation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereCourseYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\Matriculation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Users\Models\Matriculation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Users\Models\Matriculation withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Payments\Models\ArticleRequest[] $articleRequests
 * @property-read int|null $article_requests_count
 */
class Matriculation extends Model
{
    use SoftDeletes;

    protected $table = 'matriculations';

    protected $fillable = [
        'user_id',
        'code',
        'course_year',
        'lective_year',
        'created_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disciplines()
    {
        return $this
            ->belongsToMany(Discipline::class, 'matriculation_disciplines', 'matriculation_id', 'discipline_id')
            ->withPivot('exam_only');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'matriculation_classes', 'matriculation_id', 'class_id');
    }

    public function articleRequests()
    {
        return $this
            ->belongsToMany(ArticleRequest::class, 'matriculation_article_requests', 'matriculation_id', 'article_request_id')
            ->withPivot('article_id');
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
