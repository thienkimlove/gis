<?php

namespace Gis\Services\Logging;

use Gis\Helpers\LoggingAction;
use Illuminate\Support\Facades\Route;

class ApplicationLog implements ApplicationLogInterface
{
    private $_message;
    private $_prefixMessage;
    private $_currentRoute;
    private $_logger;
    const DELIMITER = '-';
    const ROUTE_DELIMITER = DIRECTORY_SEPARATOR;

    /**
     * Initializes a new instance of ApplicationException.
     *
     * @param
     *            previous[optional]
     *
     */
    public function __construct()
    {
        $config_path = config_path () . '/log4php.xml';
        \Logger::configure ( $config_path );
        $this->_currentRoute = Route::currentRouteAction ();
        $this->buildMessage ();
        $this->_logger = \Logger::getLogger ( 'GIS' );
    }
    /**
     *
     * @return the $_message
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     *
     * @return the $_prefixMessage
     */
    public function getPrefixMessage()
    {
        return $this->_prefixMessage;
    }

    /**
     *
     * @param field_type $_prefixMessage
     */
    public function setPrefixMessage($_prefixMessage)
    {
        $this->_prefixMessage = $_prefixMessage;
    }
    /**
     *
     * @param field_type $_message
     */
    public function setMessage($_message)
    {
        $this->_message = $_message;
    }
    public function getCurrentControllerAction()
    {
        $routerArr = explode ( self::ROUTE_DELIMITER, $this->_currentRoute );
        $actionStrArr = explode ( '@', end ( $routerArr ) );

        return $actionStrArr;
    }
    public function buildMessage()
    {
        $currentControllerAction = $this->getCurrentControllerAction ();
        $class = !empty($currentControllerAction [0]) ? $currentControllerAction [0] : null;
        $method = !empty($currentControllerAction [1])? $currentControllerAction [1] : null;
        $prefixMessage = '[' . $class . self::DELIMITER . $method . ']';

        $this->setPrefixMessage ( $prefixMessage );
    }
    public function logAction($message, $object = null, $logger = NULL)
    {
        $this->LogActionWithMode($message, $object);
    }

    /**
     * Log action for Mode 1
     * @param $action the information to log
     */
    public function logActionMode1($action,$object = null)
    {
        $this->LogActionWithMode($action,$object);
    }

    /**
     * Log action for Mode 2
     * @param $action the information to log
     * @param $object the data of current object to log
     */
    public function logActionMode2($action, $object)
    {
        $this->LogActionWithMode($action, $object);
    }

    /**
     * Log when start application
     */
    public function logApplicationStart(){
        $path = sprintf("%s".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR."info".DIRECTORY_SEPARATOR."%s.log",dirname(dirname($_SERVER['SCRIPT_FILENAME'])),date ( 'Y-m-d'));
        if(file_exists($path)){
            $content = file_get_contents($path);
            if(empty($content)){
                $this->logHeader();
            }
        }
        else{
            $this->logHeader();
        }
        $format = "%s\t%s\t%s\t%s\r\n";
        $message =sprintf($format,date ( 'Y/m/d'),date ( 'H:i:s'),"*","システム起動");
        $this->_logger->info ( $message, $logger = NULL );
    }
    function LogActionWithMode($action, $object = null){
        date_default_timezone_set('Asia/Tokyo');
        //check if file doesn't exist then put the header to a new file
        $path = sprintf("%s".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR."info".DIRECTORY_SEPARATOR."%s.log",dirname(dirname($_SERVER['SCRIPT_FILENAME'])),date ( 'Y-m-d'));
        if(file_exists($path)){
            $content = file_get_contents($path);
            if(empty($content)){
                $this->logHeader();
            }
        }
        else{
            $this->logHeader();
        }
        $user_code ="NULL";
        if(session('user')){
            $user_code = session('user')->user_code;
        }
        else if(session('usercode')){
            $user_code = session('usercode');
        }
        if(LoggingAction::MODE ==1){
            //Log by mode 1
            $format = "%s\t%s\t%s\t%s\r\n";
            $message =sprintf($format,date ( 'Y/m/d'),date ( 'H:i:s'),$user_code,$action);
            $this->_logger->info ( $message, $logger = NULL );
        }
        else if(LoggingAction::MODE ==2) {
            //Log by mode 2
            $format = "%s\t%s\t%s\t%s\t%s";
            $message =sprintf($format,date ( 'Y/m/d'),date ( 'H:i:s'),
                $user_code,$action,json_encode ( ( array ) $object ));
            $this->_logger->info ( $message, $logger = NULL );
        }
    }
    function logHeader(){
        $format = "%s\t%s\t%s\t%s\r\n";
        $message =sprintf($format,"年月日","時刻","ユーザコード","処理内容");
        $this->_logger->info ( $message, $logger = NULL );
    }
}
