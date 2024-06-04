<?php
/**
 * Created by Decipher Lab.
 * User: Prabakar
 * Date: 09-Apr-18
 * Time: 10:09 AM
 */

namespace app\Library;


class SecurityHelper
{
    /**
     * @var string
     */
    protected $charset;
    /**
     * @var int
     */
    protected $key;

    /**
     * NumCrypt constructor.
     *
     * @param string $charset list of chars should be used for encoding.
     * @param int $key used for bitwise xor as a simple encryption algorithm.
     */
    public function __construct($charset = '0123456789abcdefghijklmnopqrstuvwxyz', $key = 308312529)
    {
        $this->charset = (string)$charset;
        $this->key = (int)$key;
    }

    /**
     * Convert a number to the string code of minumum length $padding.
     *
     * @param int $num
     * @param int $padding
     * @return string
     */
    public function encrypt($num, $padding = 6)
    {
        $num = $this->key ^ ((int)$num);
        $base = strlen($this->charset);
        $code = [];
        while ($num > 0) {
            $code[] = $num % $base;
            $num = (int)($num / $base);
        }
        return $this->arrayCodeToString($code, $padding);
    }

    /**
     * Convert a az09 string code to a number.
     *
     * @param string $code
     *
     * @return int
     */
    public function decrypt($code)
    {
        $code = array_reverse(str_split($code));
        $base = strlen($this->charset);
        $num = 0;
        $pow = 1;
        foreach ($code as $c) {
            $num += $pow * $this->charToDigit($c);
            $pow *= $base;
        }
        return $this->key ^ (int)$num;
    }

    private function arrayCodeToString(array $code, $padding)
    {
        $str = '';
        foreach ($code as $digit) {
            $str = $this->digitToChar($digit) . $str;
        }
        return str_pad($str, $padding, $this->charset[0], STR_PAD_LEFT);
    }

    private function digitToChar($digit)
    {
        return $this->charset[$digit];
    }

    private function charToDigit($char)
    {
        return (int)strpos($this->charset, $char);
    }

    static function tamaCipher($string, $action = 'e',$secret_key)
    {
        // you may change these values to your own
        $secret_iv = 'Ioh19RDjL2RSou9tYkZT';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'e') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'd') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }else{
            $output = '*********';
        }
        return $output;
    }

    static function decipherEncryption($str){
        $items = array();
        $num = array();
        $hex = array();
        $stringPass = mb_convert_encoding($str,'UTF-16BE');
        $hash = md5($stringPass,true);
        $l = strlen($hash);
        for($i=0;$i<$l;$i++){
            $num[] = ord($hash[$i]);
        }
        foreach($num as $single){
            $hex[] = dechex($single);
        }
        foreach($hex as $h){
            $items[] = $h;
        }
        $enc_pwd = implode("",$items);
        return $enc_pwd;
    }

    static function simpleEncDec($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = '0a4%ZvMMn6B_DY5';
        $secret_iv = 'SCO#fFi_95BhqQ';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'ec') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'de') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }



}