<?php
namespace App\Models;

use App\Traits\Filterable;
use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdsCampaign extends Model  {
    use RecordSignature, Filterable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ads_campaigns';

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
        'name',
        'from_date',
        'to_date',
        'budget',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}
