<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Amenity extends Model
{
    use Filterable;

    protected $table = 'amenities';
    protected $guarded = [];

    protected $filterable = ['type', 'name', 'status'];

    public function policyTypes()
    {
        return $this->belongsTo(PolicyType::class, 'type', 'type');
    }

    public function filterName($query, $name) {
        return $query->where('name', 'like', "%$name%");
    }
}
