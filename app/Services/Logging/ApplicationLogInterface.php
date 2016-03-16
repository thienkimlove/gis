<?php

namespace Gis\Services\Logging;

/**
 * ApplicationLogger interface, using for bind with ApplicationLogService Container
 * Interface ApplicationLogInterface
 *
 * @package namespace Gis\Services\Logging;
 */
interface ApplicationLogInterface
{
    public function getMessage();
    public function getPrefixMessage();
    public function setPrefixMessage($_prefixMessage);
    public function setMessage($_message);
    public function getCurrentControllerAction();
    public function buildMessage();
    public function logAction($message, $object = null, $logger = NULL);
    public function logActionMode1($action);
    public function logActionMode2($action,$object);
}