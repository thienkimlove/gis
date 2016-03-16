<?php

namespace Gis\Exceptions;

/**
 * Declare constant value to handle exception from database side
 */
class DBApplicationException extends GisException {
	/**
	 * Delimiter for encrypt password
	 *
	 * @var FOREIGN_KEY_EX_CODE
	 */
	const FOREIGN_KEY_EX_CODE = 23503;
	
}