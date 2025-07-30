<?php
class UrlManager extends CUrlManager {
    protected function processRules()
    {
        $this->rules = Yii::app()->params['url.multi_language_links'] ? $this->getMultiLanguageRules() : $this->getSingleLanguageRules();
        parent::processRules();
    }

    public function createUrl($route, $params=array(), $ampersand='&') {
		if(!isset($params['language'])) {
			$params['language'] = Yii::app() -> language;
		}
		if(!Yii::app()->params['url.multi_language_links']) {
		    unset($params['language']);
        }

		if(isset($_GET['owner'])) {
			$params['owner']=$_GET['owner'];
		}
		return parent::createUrl($route, $params, $ampersand);
	}

    private function getMultiLanguageRules()
    {
        return array(
            'proxy'=>'PagePeekerProxy/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>' => 'site/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/feed/<_a:(rss|atom)>.xml' => 'feed/<_a>',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/cost/<domain:[\\pL\w\d\-\.]+>' => 'website/show',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/contact' => 'site/contact',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/privacy-policy' => 'site/privacy',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/terms-and-conditions' => 'site/terms',

            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy/<slug:[\\pL\d\-]+>/order-by/<order:(added_at|price)>/<sort:(asc|desc)>' => 'category/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy/<slug:[\\pL\d\-]+>/order-by/<order:(added_at|price)>' => 'category/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy/order-by/<order:(added_at|price)>/<sort:(asc|desc)>' => 'category/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy/order-by/<order:(added_at|price)>' => 'category/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy/<slug:[\\pL\d\-]+>' => 'category/index',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/buy' => 'category/index',

            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<_a:(top|upcoming)>'=> 'website/<_a>list',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/top/country/<id:[\d\w\-\_]+>'=> 'website/country',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/top/country'=> 'website/countrylist',

            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
            '<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>' => '<controller>/index',
            'admin/<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>' => 'admin/site/index',
            'admin' => 'admin/site/index',
            '<module:\w+>/<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+|\w+>' => '<module>/<controller>/<action>',
            '<module:\w+>/<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
            '<module:\w+>/<language:[a-z]{2}|[a-z]{2}_[a-z]{2}>/<controller:[\w\-]+>' => '<module>/<controller>/index',
        );
    }

	private function getSingleLanguageRules()
    {
        return array(
            'proxy'=>'PagePeekerProxy/index',
            '' => 'site/index',
            'feed/<_a:(rss|atom)>.xml' => 'feed/<_a>',
            'cost/<domain:[\\pL\w\d\-\.]+>' => 'website/show',
            'contact' => 'site/contact',
            'privacy-policy' => 'site/privacy',
            'terms-and-conditions' => 'site/terms',

            'buy/<slug:[\\pL\d\-]+>/order-by/<order:(added_at|price)>/<sort:(asc|desc)>' => 'category/index',
            'buy/<slug:[\\pL\d\-]+>/order-by/<order:(added_at|price)>' => 'category/index',
            'buy/order-by/<order:(added_at|price)>/<sort:(asc|desc)>' => 'category/index',
            'buy/order-by/<order:(added_at|price)>' => 'category/index',
            'buy/<slug:[\\pL\d\-]+>' => 'category/index',
            'buy' => 'category/index',

            '<_a:(top|upcoming)>'=> 'website/<_a>list',
            'top/country/<id:[\d\w\-\_]+>'=> 'website/country',
            'top/country'=> 'website/countrylist',

            '<module:admin>' => 'admin/site/index',
            '<module:admin>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+|\w+>' => '<module>/<controller>/<action>',
            '<module:admin>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',
            '<module:admin>/<controller:[\w\-]+>' => '<module>/<controller>/index',

            '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
            '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
            '<controller:[\w\-]+>' => '<controller>/index',
        );
    }
}