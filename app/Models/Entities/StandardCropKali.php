<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardCropNito Entity
 * Class StandardCropNito
 * @package Gis\Entities
 */

class StandardCropKali extends Model  {

    //Fields can be mass assignment.

    protected $table = 'user_fertilizer_definition_detail_kalis';

    public $timestamps = false;

    protected $fillable = [
        'user_fertilizer_definition_detail_id',
        'k',
        'ratio',
        'is_changed',
        'fertilization_standard_amount',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];


    public function standardCrop()
    {
        return $this->belongsTo('Gis\Models\Entities\StandardCrop');
    }

}