<?php
if (! function_exists ( 'buildResponseMessage' )) {
	/**
	 * Get error message.
	 *
	 * @param string $message: the message to be returned to browser
	 * @param string $code: the code to indicate status of message
	 * @param string $redirect: the url to be redirected after return
	 * @param object $data: the object to be returned to browser
	 * @return array() of object
	 */
	function buildResponseMessage($message = null, $code = null, $redirect = null,$data = null) {
		$message = empty($message) ? null : $message;
		return array (
				'message' => $message,
				'code' => $code,
				'redirect' => $redirect,
				'data' => $data 
		);
	}
}

if (! function_exists ( 'showArr' )) {
	/**
	 * out put the message to the browser.
	 *
	 * @param mixed $data: the data to be rendered to browser
	 * @param boolean $varDump        	
	 *
	 */
	function showArr($data, $varDump = false) {
		echo "<pre>";
		if ($varDump)
			var_dump ( $data );
		else
			print_r ( $data );
		echo "</pre>";
	}
}

if (! function_exists ( 'getCheckBox' )) {
	/**
	 * out put data of mixed data.
	 *
	 * @param mixed $data
	 * @param boolean $varDump
	 *
	 */
	function getCheckBox($data) {
		return true;
		//return $data == 'on' ? true : false;
		return $data = ! empty ($data ) && $data == 'on' ? true : false;
		
	}
}
if (! function_exists ( 'getDateOnly' )) {
	/**
	 * get date only from date and time.
	 *
	 * @param mixed $date
	 */
	function getDateOnly($date) {
		return date('Y:m:d', strtotime($date));

	}
}

