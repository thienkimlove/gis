<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 11:59 AM
 */

namespace Gis\Models\Entities;


use Illuminate\Database\Eloquent\Model;

class MapInfo extends Model
{

    protected $table = 'fertility_map_infos';

    public  $timestamps = false;

    protected $fillable = [
        'geo',
        'color_id',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'fertility_id',
        'fertilizer_id',
        'nitrogen',
        'fertilization_classification_code',
        'map_id',

    ];

}