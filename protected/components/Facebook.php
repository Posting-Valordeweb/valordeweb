<?php
class Facebook
{
    private static $ins;

    private $accessToken;

    private function __construct() {
        $appId = Yii::app()->params['facebook.app_id'];
        $appSecret = Yii::app()->params['facebook.app_secret'];
        $this->accessToken = "{$appId}|{$appSecret}";
    }

    public static function ins() {
        if(!self::$ins) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function getLikes($url) {
        $params = array(
            "id"=>$url,
            "fields"=>'engagement',
            "access_token"=>$this->accessToken,
        );
        return @json_decode(Helper::curl($this->buildUrl($params)), true);
    }

    private function buildUrl($params) {
        $appUrl = "https://graph.facebook.com/v18.0/";
        $requestUrl = $appUrl."?".http_build_query($params);
        return $requestUrl;
    }
}