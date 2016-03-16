<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Detail Entity
 * Class FertilizerDetail
 *
 * @package Gis\Entities
 */
class FertilizerDetail extends Model
{

    protected $table = 'fertilizer_details';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_id',
        'main_fertilizer',
        'sub_fertilizer',
        'r',
        'g',
        'b',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];

    public function fertilizer_map_infos()
    {
        return $this->hasMany('Gis\Models\Entities\FertilizerMapInfo', 'fertilizer_detail_id');
    }
}
