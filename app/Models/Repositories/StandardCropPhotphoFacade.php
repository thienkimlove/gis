<?php

namespace Gis\Models\Repositories;

/**
 * Using Facade to avoid inject Repository in Services Constructor.
 */


use Illuminate\Support\Facades\Facade;

class StandardCropPhotphoFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\StandardCropPhotphoRepository'; }
}