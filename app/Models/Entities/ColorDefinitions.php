<?php

namespace Gis\Models\Entities;
use Illuminate\Database\Eloquent\Model;

class ColorDefinitions extends Model {

    protected $table = 'color_definitions';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'r',
        'g',
        'b',
        'nitrogen',
    ];

}