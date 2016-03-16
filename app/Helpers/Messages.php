<?php
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
if (! function_exists ( 'getErrorMessage' ))
{
    /**
     * Get error message.
     *
     * @param string $id            
     * @return array()
     */
    function getErrorMessage($id = null)
    {
        if (empty ( $id ))
            return $id;
        
        $translatePathKey = getFilePathFromId ( $id );
        
        if (empty ( $translatePathKey ))
            return $translatePathKey;
        
        $arrayTranslate = loadArrayTranslate ( $translatePathKey ['path'] );
        if (empty ( $arrayTranslate ))
            return $arrayTranslate;
        
        return array_key_exists ( $translatePathKey ['key'], $arrayTranslate ) ? $arrayTranslate [$translatePathKey ['key']] : $id;
    }
}

if (! function_exists ( 'getGuid' ))
{
	/**
	 * Get error message.
	 *
	 * @param string $id
	 * @return array()
	 */
	function getGuid()
	{
        if (function_exists('com_create_guid')){
            $guid = str_replace( array("{", "}"), array('', ''), strtolower(com_create_guid()));
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $guid = strtolower(
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12));// "}"

        }


		return $guid;
	}
}
if (! function_exists ( 'getFilePathFromId' ))
{
    /**
     * Get File path & key from ID.
     *
     * @param string $id            
     * @return array()
     */
    function getFilePathFromId($id = null)
    {
        if (empty ( $id ))
            return $id;
        
        $fileArr = explode ( '.', $id );
        $key = array_pop ( $fileArr );
        return array (
                'path' => implode ( '/', $fileArr ),
                'key' => $key 
        );
    }
}

if (! function_exists ( 'loadArrayTranslate' ))
{
    /**
     * Load Array Translate BY Id.
     *
     * @param string $id            
     * @return array()
     */
    function loadArrayTranslate($path = null)
    {
        if (empty ( $path ))
            return $path;
        
        $fileLoader = new FileLoader ( new Filesystem (), app ()->langPath () );
        
        $langs = $fileLoader->load ( app ()->getLocale (), $path );
        
        return empty ( $langs ) ? null : $langs;
    }
}

if (! function_exists ( 'showArr' ))
{
    /**
     * out put data of mixed data.
     *
     * @param mixed $data            
     * @param boolean $varDump            
     *
     */
    function showArr($data, $varDump = false)
    {
        echo "<pre>";
        if ($varDump)
            var_dump ( $data );
        else
            print_r ( $data );
        echo "</pre>";
    }
}