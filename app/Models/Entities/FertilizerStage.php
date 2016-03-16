<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Map Entity
 * Class FertilizerStage
 *
 * @package Gis\Entities
 */
class FertilizerStage extends Model
{

    protected $table = 'fertilization_stages';

    public $timestamps = false;

    protected $fillable = array(
        'fertilization_stage',
        'n',
        'p',
        'k',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'fertilizer_map_id'
    );
}