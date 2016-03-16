<?php

namespace Gis\Services\Logging;

/**
 * Specifies levels of log messages.
 *
 * @author HaLM
 *        
 */
class LogLevel {
	/**
	 * Information level.
	 */
	const INFO = 0;
	
	/**
	 * Debug level.
	 */
	const DEBUG = 1;
	
	/**
	 * Warning level.
	 */
	const WARNING = 2;
	
	/**
	 * Error level.
	 */
	const ERROR = 3;
	
	/**
	 * Fatal level.
	 */
	const FATAL = 4;
}