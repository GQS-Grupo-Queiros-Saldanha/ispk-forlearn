<?php

namespace App;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Schema;

/**
 * App\Model
 *
 * @method static Builder|Model newModelQuery()
 * @method static Builder|Model newQuery()
 * @method static Builder|Model query()
 * @mixin Eloquent
 */
class Model extends BaseModel
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'created_by')) {
                if (Auth::user()) {
                    $user = Auth::user();
                    $model->created_by = $user->id;
                }
            }
        });
        static::saving(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                if (Auth::user()) {
                    $user = Auth::user();
                    //$model->created_by = $user->id;
                    $model->updated_by = $user->id;
                }
            }
        });
        static::updating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                if (Auth::user()) {
                   $user = Auth::user();
                    $model->updated_by = $user->id;

                }
            }
            
        });
        static::deleting(static function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                if (Auth::user()) {
                    $user = Auth::user();
                    $model->deleted_by = Auth::user()->id;
                }
            }
        });
        /*static::saving(static function ($model) {
            if (Schema::hasColumn($model->getTable(), 'created_by')) {
                if (Auth::user()) {
                    $model->created_by = Auth::user()->id;
                }
            }
        });

        static::updating(static function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                if (Auth::user()) {
                    $model->updated_by = Auth::user()->id;
                }
            }
        });

        static::deleting(static function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                if (Auth::user()) {
                    $model->deleted_by = Auth::user()->id;
                }
            }
        });*/
    }
}
