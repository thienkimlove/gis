<?php

namespace Gis\Services\Logging;

/**
 * Exception Logger interface, using for bind with ExceptionLogService Container
 * Interface ExceptionLogInterface
 *
 * @package namespace Gis\Services\Logging;
 */
interface ExceptionLogInterface {
	public function getMessage();
	public function getFile();
	public function getLine();
	public function getPrefixMessage();
	public function setPrefixMessage($_prefixMessage);
	public function setMessage($_message);
	public function setFile($_file);
	public function setLine($_line);
	public function getCurrentControllerAction();
	public function buildMessage();
	public function error($message = null, $error = NULL, $logger = NULL);
	public function warn($message, $error = NULL, $logger = NULL);
	public function debug($message, $logger = NULL);
	public function info($message, $object = null, $logger = NULL);
}