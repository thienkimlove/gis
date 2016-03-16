<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardCropNito Entity
 * Class StandardCropNito
 * @package Gis\Entities
 */

class StandardCropNito extends Model  {

    //Fields can be mass assignment.

    protected $table = 'user_fertilizer_definition_detail_nitos';

    public $timestamps = false;

    protected $fillable = [
        'user_fertilizer_definition_detail_id',
        'nitrogen',
        'is_changed',
        'fertilization_standard_amount',
        'ratio',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];

    public function standardCrop()
    {
        return $this->belongsTo('Gis\Models\Entities\StandardCrop','user_fertilizer_definition_detail_id');
    }

}