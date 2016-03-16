<?php

namespace Gis\Models\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class CompostStandardDryMatters extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'compost_standard_dry_matters';
    public $timestamps = false;
    protected $fillable = [
        'compost_type',
        'compost_type_description',
        'dry_matter_content',
        'n',
        'p',
        'k'
    ];

}
