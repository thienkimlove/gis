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
class FertilizerUnitPrice extends Model
{

    protected $table = 'fertilizer_unit_price';

    public $timestamps = false;

    protected $fillable = array(
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user'
    );

}