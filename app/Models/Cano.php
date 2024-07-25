<?php
namespace App\Models;

use App\Traits\Filterable;
use App\Traits\RecordSignature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cano extends Model  {
    use RecordSignature, Filterable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'canoes';

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
        'kind_of_property',
        'type',
        'steersman_id',
        'seats',
        'thumbnail_id',
        'certificate_of_registry_img',
        'certificate_of_insurance_img',
        'status',
    ];

    protected $casts = [
        'certificate_of_registry_img' => 'array',
        'certificate_of_insurance_img' => 'array',
        'created_at' => 'datetime'
    ];

    public function images()
    {
        return $this->hasMany(CanoImg::class, 'cano_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(CanoImg::class, 'thumbnail_id');
    }

    public function steerman()
    {
        return $this->belongsTo(Steerman::class, 'steersman_id');
    }
}
