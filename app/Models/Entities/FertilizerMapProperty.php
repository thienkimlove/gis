<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Fertilizer Entity
 * Class Fertilizer
 *
 * @package Gis\Entities
 */
class FertilizerMapProperty extends Model
{

    protected $table = 'fertilizer_map_properties';

    public $timestamps = false;
    public static $methodology = [1,2,3,4];
    protected $fillable = array(
        'fertilizer_map_id',
        'crops_id',
        'soil_analysis_type',
        'fertilizing_machine_type',
        'control_methodology',
        'mesh_size',
        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user',
        'one_barrel_fertilizer_name',
        'one_barrel_n',
        'one_barrel_p',
        'one_barrel_k',
        'fertilizer_price',
        'main_fertilizer_name',
        'main_fertilizer_n',
        'main_fertilizer_p',
        'main_fertilizer_k',
        'sub_fertilizer_name',
        'sub_fertilizer_n',
        'sub_fertilizer_p',
        'sub_fertilizer_k',
        'fixed_fertilizer_amount',
        'npk_type',
        'fertilizer_standard_definition_id',
        'p',
        'k',
        'id',
        'fertilizer_price_sub',
        'fertilizer_price_type',
        'fertilizer_price_sub_type',
        'main_fertilizer_usual_amount',
        'sub_fertilizer_usual_amount'
    );

    public function fertilizerMap()
    {
        return $this->belongsTo('Gis\Models\Entities\FertilizerMap');
    }

    public function filterDataFromArray($data)
    {
        $objectFields = array();
        if (!empty($data)) {
            foreach ($data as $field => $value) {
                if (in_array($field, $this->fillable) && !empty($value))
                    $objectFields[$field] = $value;
                else if($value=="0")
                    $objectFields[$field] = 0;
            }
        }

        return $objectFields;
    }
}