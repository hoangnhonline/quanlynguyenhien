<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_user = Auth::User()->id;
        });

        static::creating(function ($model) {

            $model->created_user = Auth::User()->id;
            $model->updated_user = Auth::User()->id;
        });
    }
}
