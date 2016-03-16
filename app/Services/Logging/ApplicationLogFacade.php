<?php

namespace Gis\Services\Logging;

/**
 * Using Facade to avoid inject SecurityService in Constructor.
 */
use Illuminate\Support\Facades\Facade;

class ApplicationLogFacade extends Facade {
	protected static function getFacadeAccessor() {
		return 'Gis\Services\Logging\ApplicationLogInterface';
	}
}