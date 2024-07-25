<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Steerman extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'steersman';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'bio',
        'experiences',
        'avatar',
        'degree_img',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'degree_img' => 'array',
        'created_at' => 'datetime'
    ];

}
