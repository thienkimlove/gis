<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class FertilizerEfficiencyOfCompost extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'fertilizer_efficiency_of_composts';
    public $timestamps = false;
    protected $fillable = [
        'compost_type',
        'compost_type_description',
        'application_time',
        'crops_code',
        'n',
        'p',
        'k'
    ];

}
