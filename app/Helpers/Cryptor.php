<?php
use Gis\Models\Services\UserService;
if (! function_exists ( 'encryptString' )) {
	/**
	 * Encrypt string.
	 *
	 * @param string $id        	
	 * @return array()
	 */
	function encryptString($string = null) {
		if (empty ( $string ))
			return false;
		$password = env ( 'APP_KEY' ) . UserService::DELIMITER_ENCRYPT_PASSWORD . $string;
		
		return md5 ( $password );
	}
}