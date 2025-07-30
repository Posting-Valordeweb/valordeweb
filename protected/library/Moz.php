<?php

class Moz
{
    private $token;

    public function __construct() {
        $this->token = Yii::app()->params['moz.brand_authority_token'];
    }

    public function isEnabled() {
        return !empty($this->token);
    }

    public function get($objectURL) {
        $pattern = array(
            'pda'=>0,
            'upa'=>0,
            'uid'=>0,
        );
        if(!$this->isEnabled()) {
            return $pattern;
        }

        $data = array(
            "jsonrpc" => "2.0",
            "id" => sha1(Yii::app()->securityManager->generateRandomBytes(32)),
            "method" => "beta.data.metrics.brand.authority.fetch",
            "params" => array(
                "data" => array(
                    "query" => $objectURL,
                )
            ),
        );

        $body = json_encode($data);
        $endpoint = "https://api.moz.com/jsonrpc";
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            sprintf('x-moz-token: %s', $this->token),
            sprintf('Content-Type: %s', 'application/json'),
            sprintf('Content-Length: %s', strlen($body)),
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $result = @json_decode($response, true);
        curl_close($ch);

//        var_dump($http_code);
//        var_dump($content_type);
//        var_dump($response);
//        var_dump($result);

        if ($http_code != 200 OR empty($result)) {
            return $pattern;
        }

        if (isset($result['error']) OR !isset($result['result']['brand_authority_score'])) {
            return $pattern;
        }

        return array(
            'pda' => $result['result']['brand_authority_score'],
            'upa' => 0,
            'uid' => 0,
        );

        // DEPRECATED API

        // Set your expires times for several minutes into the future.
        // An expires time excessively far in the future will not be honored by the Mozscape API.
        $expires = time() + 300;
        // Put each parameter on a new line.
        $stringToSign = $this->creds['accessID']."\n".$expires;
        // Get the "raw" or binary output of the hmac hash.
        $binarySignature = hash_hmac('sha1', $stringToSign, $this->creds['secretKey'], true);
        // Base64-encode it and then url-encode that.
        $urlSafeSignature = urlencode(base64_encode($binarySignature));
        // Add up all the bit flags you want returned.
        // Learn more here: https://moz.com/help/guides/moz-api/mozscape/api-reference/url-metrics
        $cols = 34359738368+68719476736+2048;
        // Put it all together and you get your request URL.
        // This example uses the Mozscape URL Metrics API.
        $requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($objectURL)."?Cols=".$cols."&AccessID=".$this->creds['accessID']."&Expires=".$expires."&Signature=".$urlSafeSignature;

        if(!$response = @json_decode(Helper::curl($requestUrl), true)) {
            return $pattern;
        }

        if(!isset($response['pda'], $response['upa'], $response['uid'])) {
            return $pattern;
        }

        return array(
            'pda'=>(float) $response['pda'],
            'upa'=>(float) $response['upa'],
            'uid'=>(int) $response['uid'],
        );
    }
}