<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Crop Entity
 * Class Crop
 *
 * @package Gis\Entities
 */
class FertilizerMapPayment extends Model
{

    // Fields can be mass assignment.
    protected $table = 'fertilizer_map_payments';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_id',
        'user_code',
        'unit_price',
        'area',
        'payment_date',
        'download_date',
        'is_paid',
        'remark',
        'download_id',
        'id',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'crops_id'
    ];
    public function fertilizer_maps()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilizerMap', 'fertilizer_id');
    }


}