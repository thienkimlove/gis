<?php

namespace Gis\Models\Repositories;


use Illuminate\Support\Facades\Facade;

class FertilityMapFacade  extends  Facade{

  protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilityMapRepository'; }

}