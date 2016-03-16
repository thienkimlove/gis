<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Map Entity
 * Class FertilizerMap
 *
 * @package Gis\Entities
 */
class FertilizerMapInfo extends Model
{

    protected $table = 'fertilizer_map_infos';

    public $timestamps = false;

    protected $fillable = array(
        'geo',
        'fertilizer_id',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'main_fertilizer',
        'sub_fertilizer',
        'r',
        'g',
        'b'
    );

    public function folderLayer()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilizerMap', 'fertilizer_id');
    }

    public function fertilizerDetail()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilizerDetail', 'fertilizer_detail_id');
    }
}