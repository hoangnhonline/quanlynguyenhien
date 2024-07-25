<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Policy extends Model
{
    use Filterable;

    protected $table = 'policies';
    protected $guarded = [];
    public $timestamps = true;

    protected $filterable = ['type', 'title'];


    #loai hinh
    public function policyType()
    {
        return $this->belongsTo(PolicyType::class, 'type', 'type');
    }

    public
    function filterTitle($query, $title)
    {
        return $query->where('title', 'like', "%$title%");
    }


}
