<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardCropNito Entity
 * Class StandardCropNito
 * @package Gis\Entities
 */

class SystemFertilizerDefinitionDetailNito extends Model  {

    //Fields can be mass assignment.

    protected $table = 'system_fertilizer_definition_detail_nitos';

    public $timestamps = false;

    protected $fillable = [
        'n',
        'n_amount',
        'ratio',
        'crops_id',
        'fertilizer_standard_definition_id',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time',
        'division_amount1',
        'division_amount2',
        'division_amount3',
        'division_amount4',
        'division_amount5',
        'division_amount6',
        'division_amount7',
        'division_amount8',
        'division_amount9',
        'division_amount10',
        'division_amount11',
        'division_amount12',
        'division_amount13',
        'division_amount14',
        'division_amount15',
        'division_amount16',
        'division_amount17',
        'division_amount18',
        'division_amount19',
        'division_amount20'
    ];

    public function fertilizerStandardDefinition()
    {
        return $this->belongsTo('Gis\Models\Entities\Fertilizer','fertilizer_standard_definition_id');
    }

}