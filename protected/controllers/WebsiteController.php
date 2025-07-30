<?php
Yii::import("ext.ecountrylist.*");
Yii::import("application.library.Moz");
Yii::import("application.library.SearchStatToAlexa");

class WebsiteController extends FrontController {
	public function actionCalculate() {
		if(isset($_GET['CalculationForm']) AND is_array($_GET['CalculationForm'])) {
			$form=new CalculationForm();
			$form->attributes=$_GET['CalculationForm'];
			if(!$form->validate()) {
				$this->jsonResponse($form -> getErrors());
			}
			$website=$form->getWebsite();
			$redirect = strtr(Yii::app()->request->getQuery('redirect'), array(
				'__ID__'=> $website->id,
				'__DOMAIN__'=>$website->domain,
			));
			$instant=(bool) Yii::app()->request->getQuery('instant');
			if($instant) {
				$this->redirect($redirect);
			} else {
				$this->jsonResponse($redirect);
			}
		}
	}

	public function actionShow($domain) {
		if(!$website = Website::model()->with(array(
			"alexa", "antivirus", "catalog", "location", "meta_tags", "search_engine", "social", "whois", "moz",
			"sale"=>array(
				"with"=>array(
					"category"=>array(
						"with"=>array(
							"translations"=>array(
								"scopes"=>array("current_lang"),
							),
						),
					),
					"user"=>array(
						"select"=>"id",
					),
				),
			),
		))->findByAttributes(array(
			"md5domain"=>md5($domain),
		))) {
			if(Yii::app()->params["site_cost.auto_calculation"] AND !Yii::app()->params["site_cost.captcha"]) {
				$form=new CalculationForm;
				$form->domain=$domain;
				if($form->validate()) {
					$this->redirect($this->createUrl("website/show", array("domain"=>$domain)));
				}
			}
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		Yii::import("application.library.Estimator");
		$serpToAlexa = new SearchStatToAlexa(
            $website->search_engine->google_index,
            $website->search_engine->bing_index,
            $website->search_engine->yahoo_index
        );
        $estimator = new Estimator($serpToAlexa->convert());
		//$estimator = new Estimator($website->alexa->rank);
		
		$thumbnail=WebsiteThumbnail::getThumbData(array(
			'url'=>$website->domain,
			'size'=>'l',
		));

		$meta_params = array(
            "{Website}"=>$website->idn,
            "{Cost}"=>html_entity_decode(Helper::p($website->price)),
            "{Title}"=>html_entity_decode($website->meta_tags->title),
            "{Keywords}"=>html_entity_decode($website->meta_tags->keywords),
            "{Description}"=>html_entity_decode($website->meta_tags->description)
        );

		$this->title = Yii::t("website", "{Website} worth is {Cost}", $meta_params);
        /**
         * @var $cs CClientScript
         */
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag(Yii::t("website", "website keywords", $meta_params), 'keywords');
        $cs->registerMetaTag(Yii::t("website", "website description", $meta_params), 'description');

		$url=$this->createAbsoluteUrl("website/show", array("domain"=>$website->domain));
		$cs->registerMetaTag(Yii::t("website", "Website cost"),null,null,array('property'=>'og:title'));
		$cs->registerMetaTag($this->title,null,null,array('property'=>'og:description'));
		$cs->registerMetaTag($url,null,null,array('property'=>'og:url'));
		$cs->registerMetaTag(WebsiteThumbnail::getOgImage(array(
            'url'=>$website->domain,
            'size'=>'m',
        )),null,null,array('property'=>'og:image', 'encode'=>false));


		$update = (time() - strtotime($website->modified_at)) > Yii::app() -> params["site_cost.cache"];
		$time = Yii::app()->dateFormatter->formatDateTime($website->modified_at, 'long', 'medium');
		if(!Yii::app()->user->isGuest AND $website->sale) {
			$box=Yii::app()->innerMail->box(Yii::app()->user->loadModel());
			$block=$box->getBlock($website->sale->user->id);
		} else {
			$block=null;
		}

		$updateLink=$this->createUrl("website/calculate", array(
				"instant"=>1,
				"redirect"=>$this->createUrl("website/show", array("domain"=>$website->domain)),
				"CalculationForm"=>array(
						"domain"=>$website->domain,
				)
		));

		$widget=$this->renderPartial("/{$this->_end}/website/widget", array(
			"url"=>$this->createAbsoluteUrl("website/show", array("domain"=>$website->domain)),
			"domain"=>$website->idn,
			"price"=>Helper::p($website->price),
		), true);

        $this->render("show", array(
			"website"=>$website,
			"update"=>$update,
			"time"=>$time,
			"widget"=>$widget,
			"block"=>$block,
			"thumbnail"=>$thumbnail,
			"updateLink"=>$updateLink,
			"moz"=>new Moz(),
			"country"=>ECountryList::getInstance(Yii::app()->language),
			"estimator"=>$estimator,
			"dailyVisitors"=>$estimator->getEstimatedUniqueDailyVisitors(),
			"dailyPageviews"=>$estimator->getEstimatedDailyPageViews(),
			"dailyAdsRevenue"=>$estimator->getEstimatedDailyAdsRevenue(),
			"monthlyVisitors"=>$estimator->getEstimatedUniqueMonthlyVisitors(),
			"monthlyPageviews"=>$estimator->getEstimatedMonthlyPageViews(),
			"monthlyAdsRevenue"=>$estimator->getEstimatedMonthlyAdsRevenue(),
			"yearlyVisitors"=>$estimator->getEstimatedUniqueYearlyVisitors(),
			"yearlyPageviews"=>$estimator->getEstimatedYearlyPageViews(),
			"yearlyAdsRevenue"=>$estimator->getEstimatedYearlyAdsRevenue(),
		));
	}

	public function actionTopList() {
		$page = (int) Yii::app()->request->getQuery('page', 1);
		$this->title = Yii::t("website", "Top websites by price. Page {PageNr}", array(
			"{PageNr}"=>$page,
		));
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag($this->title, 'keywords');
		$cs->registerMetaTag($this->title, 'description');

        $total=Website::model()->cache(3600)->count();
		$widget = $this->widget('application.widgets.WebsiteList', array(
			"config"=>array(
				"criteria"=>array(
					"order"=>"t.price DESC"
				),
                "totalItemCount"=>$total,
			),
		), true);
		$this->render("website_list", array(
			"widget"=>$widget,
			'afterHeader'=>$this->renderPartial("top_breadcrumbs", array(), true),
		));
	}

	public function actionCountryList($id = null, $page = null) {
		$page = $page == null ? 1 : $page;
		$this->title = Yii::t("website", 'Top websites by countries. Diagram. Page {PageNr}', array(
			"{PageNr}"=>$page,
		));
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag($this->title, 'keywords');
		$cs->registerMetaTag($this->title, 'description');

		$raw = WebdataLocation::model()->cache(60*60*5)->countryGroup()->findAll();
		$dataProvider = new CArrayDataProvider($raw, array(
			'pagination' => array(
				'pageSize' => Yii::app()->params['site_cost.countries_per_page'],
			),
		));
		$topCountries=array();
		$sum = 0;
		$top = 10;
		$topCountries = array_slice($raw, 0, $top);
		foreach($topCountries as $row) {
			$sum += $row->countryTotal;
		}

		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/jquery.flot.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/jquery.flot.pie.js');

		$this->render("country_list", array(
			"sum" => $sum,
			"topCountries" => $topCountries,
			"top" => $top,
			"dataProvider" => $dataProvider,
			"country" => ECountryList::getInstance(Yii::app()->language),
		));
	}

	public function actionCountry($id, $page = null) { // Country
		$page = $page == null ? 1 : $page;
		$country = ECountryList::getInstance(Yii::app()->language);
		$countryName = $country->getCountryName($id);
		$this->title = Yii::t("website", "Top websites in {Country}. Page {PageNr}", array(
			"{Country}"=>$countryName,
			"{PageNr}"=>$page,
		));
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag($this->title, 'keywords');
		$cs->registerMetaTag($this->title, 'description');

		$criteria = new CDbCriteria;
		$criteria->condition="country_code=:country_code";
		$criteria->params=array(":country_code"=>$id);
		$total=WebdataLocation::model()->cache(60*60)->count($criteria);

		$widget = $this->widget('application.widgets.WebsiteList', array(
			"config"=>array(
				"totalItemCount"=>$total,
				"criteria"=>array(
					"with"=>array(
						"location" => array(
							"select"=>"country_code",
						),
					),
					"condition"=>"location.country_code=:country_code",
					"params"=>array(":country_code"=>$id),
					"order"=>"t.price DESC"
				),
			),
		), true);

		$this->render("website_list", array(
			"afterHeader"=>$this->renderPartial("top_breadcrumbs", array(), true),
			"widget"=>$widget,
		));
	}

	public function actionUpcomingList() {
		$page = (int) Yii::app()->request->getQuery('page', 1);
		$params=array(
			"{PageNr}"=>$page
		);
		$this->title=Yii::t("website", "List of upcoming websites. Page {PageNr}", $params);
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag($this->title, 'keywords');
		$cs->registerMetaTag($this->title, 'description');

        $total=Website::model()->cache(3600)->count();
		$widget = $this->widget('application.widgets.WebsiteList', array(
            "config"=>array(
                "totalItemCount"=>$total,
            )
		), true);

		$this->render("website_list", array(
			"widget"=>$widget,
		));
	}

	public function actionSell() {
		$this->title=Yii::t("sale_instruction", "Sell Websites - Sell Domains");
		$params=array(
			"{Portal}"=>Helper::getInstalledUrl(),
		);
		$cs=Yii::app()->clientScript;
		$cs->registerMetaTag(Yii::t("sale_instruction", "Sale instruction page keywords", $params), 'keywords');
		$cs->registerMetaTag(Yii::t("sale_instruction", "Sale instruction description", $params), 'description');

		$this->render("sell", array(
			"brandUrl"=>Helper::getInstalledUrl(),
		));
	}
}