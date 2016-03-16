<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FertilityMapSelectionInfo
 *
 * @package Gis\Models\Entities
 */
class FertilityMapSelectionInfo extends Model
{

    protected $table = 'fertility_map_selection_infos';

    public $timestamps = false;

    protected $fillable = array(
        'fertility_map_selection_id',
        'map_info_id',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user'
    );

    public function fertilityMapSelection()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilityMapSelection', 'fertility_map_selection_id');
    }

    public function mapInfos()
    {
        return $this->hasMany('Gis\Models\Entities\MapInfo', 'map_info_id');
    }

}