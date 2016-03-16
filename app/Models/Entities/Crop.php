<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Crop Entity
 * Class Crop
 * 
 * @package Gis\Entities
 */
class Crop extends Model
{
    
    // Fields can be mass assignment.
    protected $table = 'crops_definitions';

    public $timestamps = false;

    protected $fillable = [
            'id',
            'crops_name',
            'descriptions',
            'crops_code',
            'ins_user',
            'upd_user',
            'ins_time',
            'upd_time'
    ];

    public function nito()
    {
        return $this->hasMany('Gis\Models\Entities\DefaultStandardCropNito', 'crops_id');
    }
    public function photpho()
    {
        return $this->hasMany('Gis\Models\Entities\DefaultStandardCropPhotpho', 'crops_id');
    }
    public function kali()
    {
        return $this->hasMany('Gis\Models\Entities\DefaultStandardCropKali', 'crops_id');
    }
}