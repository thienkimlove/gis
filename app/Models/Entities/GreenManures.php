<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class GreenManures extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'green_manures';
    public $timestamps = false;
    protected $fillable = [
        'green_manure_type',
        'green_manure_type_description',
        'cropping_type',
        'cropping_type_description',
        'exchangeable_potassium_content_type',
        'exchangeable_potassium_content',
        'crops_code',
        'n',
        'p',
        'k',
        'standard_dry_weight',
        'standard_CN_ratio'
    ];

}
