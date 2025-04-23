<?php

namespace App\Modules\Payments\Models;

use App\Modules\GA\Models\Course;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Payments\Models\ArticleMonthlyCharge
 *
 * @property-read \App\Modules\Payments\Models\Article $article
 * @property-read \App\Modules\GA\Models\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $article_id
 * @property int $course_id
 * @property int|null $course_year
 * @property int $start_month
 * @property int $end_month
 * @property int $charge_day
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereChargeDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereCourseYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereEndMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleMonthlyCharge whereStartMonth($value)
 */
class ArticleMonthlyCharge extends Model
{
    public $timestamps = false;

    protected $fillable = ['course_id', 'course_year', 'start_month', 'end_month', 'charge_day'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
