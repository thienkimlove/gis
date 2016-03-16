<?php
namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * StandardUser Entity
 * Class StandardUser
 *
 * @package Gis\Entities
 */
class StandardCrop extends Model
{
    
    // Fields can be mass assignment.
    protected $table = 'user_fertilizer_definition_details';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_standard_definition_id',
        'crops_id',
        'remarks',
        'not_available',
        'fertilization_standard_amount_n',
        'fertilization_standard_amount_p',
        'fertilization_standard_amount_k',
        'user_code',
        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];

    public function crop()
    {
        return $this->belongsTo('Gis\Models\Entities\Crop', 'crops_id');
    }

    public function fertilizer()
    {
        return $this->belongsTo('Gis\Models\Entities\Crop', 'fertilizer_standard_definition_id');
    }

    public function nito()
    {
        return $this->hasMany('Gis\Models\Entities\StandardCropNito', 'user_fertilizer_definition_detail_id');
    }

    public function kali()
    {
        return $this->hasMany('Gis\Models\Entities\StandardCropKali', 'user_fertilizer_definition_detail_id');
    }

    public function photpho()
    {
        return $this->hasMany('Gis\Models\Entities\StandardCropPhotpho', 'user_fertilizer_definition_detail_id');
    }
}