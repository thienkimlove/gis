<?php

namespace Gis\Services\Logging;

use Illuminate\Support\Facades\Route;

class ExceptionLog implements ExceptionLogInterface {
	private $_message;
	private $_file;
	private $_line;
	private $_prefixMessage;
	private $_currentRoute;
	private $_logger;
	const DELIMITER = '-';
	const ROUTE_DELIMITER = DIRECTORY_SEPARATOR;
	const SYSTEM_EXCEPTION = 2;
	
	/**
	 * Initializes a new instance of ApplicationException.
	 *
	 * @param
	 *        	message[optional]
	 * @param
	 *        	previous[optional]
	 *        	
	 */
	public function __construct(\Exception $exception) {
		$config_path = config_path () . '/log4php.xml';
		\Logger::configure ( $config_path );
		$this->_message = $exception->getMessage ();
		
		$this->_file = $exception->getFile ();
		$this->_line = $exception->getLine ();
		
		$this->_currentRoute = Route::currentRouteAction ();
		
		$this->buildMessage ();
		$this->_logger = \Logger::getLogger ( 'GIS' );
	}
	/**
	 *
	 * @return the $_message
	 */
	public function getMessage() {
		return $this->_message;
	}
	
	/**
	 *
	 * @return the $_file
	 */
	public function getFile() {
		return $this->_file;
	}
	
	/**
	 *
	 * @return the $_line
	 */
	public function getLine() {
		return $this->_line;
	}
	
	/**
	 *
	 * @return the $_prefixMessage
	 */
	public function getPrefixMessage() {
		return $this->_prefixMessage;
	}
	
	/**
	 *
	 * @param field_type $_prefixMessage        	
	 */
	public function setPrefixMessage($_prefixMessage) {
		$this->_prefixMessage = $_prefixMessage;
	}
	/**
	 *
	 * @param field_type $_message        	
	 */
	public function setMessage($_message) {
		$this->_message = $_message;
	}
	
	/**
	 *
	 * @param field_type $_file        	
	 */
	public function setFile($_file) {
		$this->_file = $_file;
	}
	
	/**
	 *
	 * @param field_type $_line        	
	 */
	public function setLine($_line) {
		$this->_line = $_line;
	}
	public function getCurrentControllerAction() {
		$routerArr = explode ( self::ROUTE_DELIMITER, $this->_currentRoute );
		$actionStrArr = explode ( '@', end ( $routerArr ) );
		return $actionStrArr;
	}
	
	/**
	 *
	 * @param unknown $type        	
	 */
	public function buildMessage() {
		$currentControllerAction = $this->getCurrentControllerAction ();
		$class = empty($currentControllerAction [0]) ? null : $currentControllerAction [0];
		$method =empty($currentControllerAction [1]) ? null : $currentControllerAction [1];
		$prefixMessage = '[' . $this->_file . self::DELIMITER . $this->_line . ']' . self::DELIMITER . '[' . $class . self::DELIMITER . $method . ']';
		
		$this->setPrefixMessage ( $prefixMessage );
	}
	public function error($message = null, $error = NULL, $logger = NULL) {
		$message = empty ( $message ) ? $this->_message : $message;
		$message = $this->_prefixMessage . self::DELIMITER . '[' . $message . ']';
		$this->_logger->error ( $message, $error = NULL, $logger = NULL );
	}
	public function warn($message, $error = NULL, $logger = NULL) {
		$message = $this->_prefixMessage . self::DELIMITER . '[' . $message . ']';
		$this->_logger->warn ( $message, $error = NULL, $logger = NULL );
	}
	public function debug($message, $logger = NULL) {
		$message = $this->_prefixMessage . self::DELIMITER . '[' . $message . ']';
		$this->_logger->debug ( $message, $logger = NULL );
	}
	public function info($message, $object = null, $logger = NULL) {
		$message = $this->_prefixMessage . self::DELIMITER . '[' . $message . ']';
		if (! empty ( $object ))
			$message .= self::DELIMITER . '[' . json_encode ( ( array ) $object ) . ']';
		$this->_logger->info ( $message, $logger = NULL );
	}
}
