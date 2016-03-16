<?php namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Entity
 * Class Fertilizer
 * @package Gis\Entities
 */

class Fertilizer extends Model  {

    //Fields can be mass assignment.

    protected $table = 'fertilizer_standard_definitions';

    public $timestamps = false;

    protected $fillable = [
        'created_by',
        'fertilization_standard_name',
        'range_of_application',
        'notes',
        'remarks',
        'not_available',
        'initial_display',
        'basis_of_calculation',
        
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];

    public function standardUsers()
    {
        return $this->hasMany('Gis\Models\Entities\StandardUser','fertilizer_standard_definition_id');
    }

    public function standardCrops()
    {
        return $this->hasMany('Gis\Models\Entities\StandardCrop','fertilizer_standard_definition_id');
    }

    public function systemStandardNitos()
    {
        return $this->hasMany('Gis\Models\Entities\SystemFertilizerDefinitionDetailNito','fertilizer_standard_definition_id');
    }
    public function systemStandardPhotphos()
    {
        return $this->hasMany('Gis\Models\Entities\SystemFertilizerDefinitionDetailPhotpho','fertilizer_standard_definition_id');
    }
    public function systemStandardKalis()
    {
        return $this->hasMany('Gis\Models\Entities\SystemFertilizerDefinitionDetailKali','fertilizer_standard_definition_id');
    }


//     public function crop()
//     {
//     	return $this->belongsTo('Gis\Models\Entities\Crop', 'crops_id');
//     }
}