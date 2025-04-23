<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;


/**
 * App\Modules\Users\Models\UserCandidate
 *
 * @property int $user_id
 * @property string $code
 * @property string $created_at
 * @property-read \App\Modules\Users\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\UserCandidate whereUserId($value)
 * @mixin \Eloquent
 */
class UserCandidate extends Model
{

    protected $table = 'user_candidate';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'code',
        'year',
        'year_fase_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
