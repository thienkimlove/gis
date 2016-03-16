<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class FertilizationPrice extends Model implements Transformable
{
    use TransformableTrait;
    public $timestamps = false;
    protected $table = 'fertilizer_unit_price';
    protected $fillable = [

        'start_date',
        'end_date',
        'price',

        'ins_user',
        'upd_user',
        'ins_time',
        'upd_time'
    ];

}
