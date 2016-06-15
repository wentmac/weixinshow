<?php
/**
 * $des = new des();
 * $des->setKey('asdfasfasdfasfafa');
 * $des->encode('asdfa'):
 * $des->decode('asdfa'):
 */
class des
{

    private $key = '8-475A-93EE-14D97863A936';
    private $iv = '0102030405060708';

    public function __construct()
    {
        
    }

    /**
     * @brief      设置加密密钥
     * @param string $key  加密密钥
     * @return     void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    public function encode($string)
    {
        $iv = pack("H16", $this->iv);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        mcrypt_generic_init($td, $this->key, $iv);

        $str = base64_encode(mcrypt_generic($td, self::pkcs5_pad($string, 8)));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $str;
    }

    public function decode($string)
    {        
        $key = $this->key;
        $iv = pack("H16", $this->iv);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');

        mcrypt_generic_init($td, $key, $iv);
        $ttt = self::pkcs5_unpad(mdecrypt_generic($td, base64_decode($string)));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ttt;
    }

    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

}