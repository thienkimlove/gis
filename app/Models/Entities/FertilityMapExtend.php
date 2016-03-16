<?php
/**
 * User: smagic39
 * Date: 6/8/15
 * Time: 11:01 AM
 */
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FertilityMap
 *
 * @package Gis\Models\Entities
 */
class FertilityMapExtend extends Model
{

    protected $table = 'fertility_map_extends';

    public $timestamps = false;

    protected $fillable = array(
        'layer_id',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'map_name',
        'extend_x1',
        'extend_y1',
        'extend_x2',
        'extend_y2',
        'central_point_x1',
        'central_point_y1',
        'central_point_x2',
        'central_point_y2',
        'layers',
        'id',
        'legend_list',
    );

}