<?php

namespace App\Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class CurrentAccountObservations extends Model
{
    protected $table = 'current_account_observations';

    protected $fillable = ['user_id', 'observation', 'file'];

    public function scopeObservations($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeFile($query, $id)
    {
        return $query->where('id', $id);
    }
}
