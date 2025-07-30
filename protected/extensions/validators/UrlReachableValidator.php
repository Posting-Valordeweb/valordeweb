<?php
class UrlReachableValidator  extends CValidator {
    public $enableClientValidation = false;
    public $message = "Url is not reachable";
    public $statuses = array(200);
    public $timeOut = 5;
    public $allowEmpty = false;
    public $skipOnError = true;

    protected function validateAttribute($object, $attribute) {
        $url = $object -> $attribute;
        $curl = curl_init($url);
        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this -> timeOut);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //do request
        $result=Helper::curl_exec($curl);
        $ret = false;
        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (in_array($statusCode, $this -> statuses)) {
                $ret = true;
            }
        }
        curl_close($curl);
        if(!$ret) {
            $this -> addError($object, $attribute, $this -> message);
        }
    }
}