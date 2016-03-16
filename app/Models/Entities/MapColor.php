<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * MapColor Entity
 * Class MapColor
 * 
 * @package Gis\Entities
 */
class MapColor extends Model
{
    
    // Fields can be mass assignment.
    protected $table = 'fertilization_map_of_initial_colors';

    public $timestamps = false;
    const MAX_SIZE_COLOR = 11;

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

}