<?php

namespace Antenna\EmailForDownload\Inc;

class Crypter
{
    /** @var string */
    private $secret_key = 'email_for_download_secret_key';

    /** @var string */
    private $secret_iv = 'email_for_download_secret_iv';

    /** @var string */
    private $encrypt_method = 'AES-256-CBC';

    /**
     * @param $string
     *
     * @return string
     */
    public function encrypt($string)
    {
        $key = hash('sha256', $this->secret_key);
        $iv  = substr(hash('sha256', $this->secret_iv), 0, 16);

        return base64_encode(openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv));
    }

    /**
     * @param $string
     *
     * @return false|string
     */
    public function decrypt($string)
    {
        $key = hash('sha256', $this->secret_key);
        $iv  = substr(hash('sha256', $this->secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($string), $this->encrypt_method, $key, 0, $iv);
    }

}