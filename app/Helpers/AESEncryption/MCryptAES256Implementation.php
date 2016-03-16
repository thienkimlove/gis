<?php

namespace Gis\Helpers\AESEncryption;

class MCryptAES256Implementation implements AES256Implementation
{
    const BLOCK_SIZE = 16; // 128 bits
    const KEY_SIZE = 32; // 256 bits
    const MY_MCRYPT_CIPHER = MCRYPT_RIJNDAEL_128; //AES
    const MY_MCRYPT_MODE = MCRYPT_MODE_CBC; //AES

    public function checkDependencies()
    {
        $function_list = array(
            "mcrypt_create_iv",
            "mcrypt_encrypt",
            "mcrypt_decrypt",
        );
        foreach ($function_list as $func) {
            if (!function_exists($func)) {
                throw new Exception("Missing function dependency: " . $func);
            }
        }
    }

    /**
     * Create the IV value to encrypt and decrypt
     * @return string
     */
    public function createIV()
    {
        return mcrypt_create_iv( self::BLOCK_SIZE, MCRYPT_RAND );
    }

    /**
     * Create random key for IV value
     * @return string
     */
    public function createRandomKey()
    {
        return mcrypt_create_iv( self::KEY_SIZE, MCRYPT_RAND );
    }

    /**
     * Encrypt file by AES encryption
     * @param $the_data the data to be encrypted
     * @param $iv the value of IV to encrypt
     * @param $enc_key the key or password to encrypt
     * @return the encrypted file
     */
    public function encryptData($the_data, $iv, $enc_key)
    {
        return mcrypt_encrypt( self::MY_MCRYPT_CIPHER, $enc_key, $the_data , self::MY_MCRYPT_MODE , $iv );
    }

    /**
     * decrypt file by AES encryption
     * @param $the_data
     * @param $iv
     * @param $enc_key
     * @return string
     */
    public function decryptData($the_data, $iv, $enc_key)
    {
        return mcrypt_decrypt( self::MY_MCRYPT_CIPHER, $enc_key, $the_data , self::MY_MCRYPT_MODE , $iv );
    }
}