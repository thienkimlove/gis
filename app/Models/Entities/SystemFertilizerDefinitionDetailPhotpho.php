<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class SystemFertilizerDefinitionDetailPhotpho extends Model
{

    protected $table = 'system_fertilizer_definition_detail_photphos';

    public $timestamps = false;

    protected $fillable = [
        'fertilizer_standard_definition_id',
        'crops_id',
        'p',
        'ratio',
        'fertilization_standard_amount',
        'assessment',

        'ins_time',
        'upd_time',
        'ins_user',
        'upd_user'
    ];

}