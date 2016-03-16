<?php

namespace Gis\Models\Repositories;


use Illuminate\Support\Facades\Facade;

class FertilityMapSelectionInfoFacade  extends  Facade{

  protected static function getFacadeAccessor() { return 'Gis\Models\Repositories\FertilityMapSelectionInfoRepository'; }

}