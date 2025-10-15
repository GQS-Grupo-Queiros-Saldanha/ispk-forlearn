<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

class WhatsappChecked extends Model
{
    use SoftDeletes;

    protected $table = "users";

    protected $fillable = [
        'id',
        'user_whatsapp'
    ];
    

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


}
