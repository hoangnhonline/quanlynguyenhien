<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CustomerSource extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_sources';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    public function parent()
    {
        return $this->belongsTo(CustomerSource::class, 'parent_id');
    }
    public function childs()
    {
        return $this->hasMany(CustomerSource::class, 'parent_id');
    }
}
