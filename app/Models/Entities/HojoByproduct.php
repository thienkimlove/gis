<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class HojoByproduct extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'hojo_byproducts';
    public $timestamps = false;
    protected $fillable = [
        'organic_type',
        'organic_type_description',
        'processing_method_type',
        'processing_method',
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
