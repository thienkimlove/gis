<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardCropNito Entity
 * Class StandardCropNito
 * @package Gis\Entities
 */

class DefaultStandardCropKali extends Model  {

    //Fields can be mass assignment.

    protected $table = 'fertilizer_definition_default_kalis';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];
    

}