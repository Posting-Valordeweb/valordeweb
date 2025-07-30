<?php
/**
Error codes:
 102. Couldn't grab html from website
 103. Website contains badwords
 201. Error while inserting data
 202. Error while updating data
*/
Yii::import('application.library.*');
set_time_limit(0);
class CalculateCommand extends CConsoleCommand {
	protected $domain;
	protected $url;
    protected $idn;
    protected $ip;
    protected $html;
    protected $errorcode;
    protected $price;
	protected $alexa = array();
	protected $antivirus = array();
	protected $catalog = array();
	protected $location = array();
	protected $metatag = array();
	protected $searchengine = array();
	protected $moz = array();
	protected $social = array();
    /**
     * @var MetaTags
     */
	protected $Metatags;
	protected $whois;

	private function _init($domain, $idn, $ip) {
		$this->domain = $domain;
		$this->ip = $ip;
		$this->idn =  $idn;
	}

	public function actionInsert($domain, $idn, $ip) {
		$this->_init($domain, $idn, $ip);

		if(!$this -> grabHtml()) {
			return $this -> errorcode;
		}

		if($this -> existsBadwords()) {
			return $this -> errorcode;
		}

		$this->collectWebsiteData();
		$this->setWebsiteCost();
		
		// Begin transaction
		$transaction = Yii::app() -> db -> beginTransaction();
		try {
            $website = new Website;

            $website->attributes=array("price"=>$this->price, "domain"=>$this->domain, "idn"=>$this->idn);
            if(!$website->save(false)) { throw new Exception("An error occuried while inserting webdata_main"); }
            $wid = $website->id;

            $alexa = new WebdataAlexa;
            $alexa->attributes = array_merge(array("wid"=>$wid), $this->alexa);
            if(!$alexa->save(false)) { throw new Exception("An error occurred while inserting webdata_alexa"); }

            $antivirus = new WebdataAntivirus;
            $antivirus->attributes = array_merge(array("wid"=>$wid), $this->antivirus);
            if(!$antivirus->save(false)) { throw new Exception("An error occurred while inserting webdata_antivirus"); }

            $catalog = new WebdataCatalog;
            $catalog->attributes = array_merge(array("wid"=>$wid), $this->catalog);
            if(!$catalog->save(false)) { throw new Exception("An error occurred while inserting webdata_catalog"); }

            $location = new WebdataLocation;
            $location->attributes = array_merge(array("wid"=>$wid), $this->location);
            if(!$location->save(false)) { throw new Exception("An error occurred while inserting webdata_location"); }

            $meta_tags = new WebdataMetaTags;
            $meta_tags->attributes = array_merge(array("wid"=>$wid), $this->metatag);
            if(!$meta_tags->save(false)) { throw new Exception("An error occurred while inserting webdata_metatags"); }

            $search_engine = new WebdataSearchEngine;
            $search_engine->attributes = array_merge(array("wid"=>$wid), $this->searchengine);
            if(!$search_engine->save(false)) { throw new Exception("An error occurred while inserting webdata_search_engine"); }

            $social = new WebdataSocial;
            $social->attributes = array_merge(array("wid"=>$wid), $this->social);
            if(!$social->save(false)) { throw new Exception("An error occurred while inserting webdata_social"); }

            $whois = new WebdataWhois;
            $whois->attributes = array("wid"=>$wid, 'text'=>$this->whois);
            if(!$whois->save(false)) { throw new Exception("An error occurred while inserting webdata_whois"); }

            $moz = new WebdataMoz;
            $moz->attributes = array_merge(array("wid"=>$wid), $this->moz);
            if(!$moz->save(false)) { throw new Exception("An error occurred while inserting webdata_moz"); }

            $transaction->commit();
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.command.calculate.insert');
			return 201;
		}
		return 0;
	}

	public function actionUpdate($domain, $idn, $ip, $wid) {
		$this->_init($domain, $idn, $ip);
		if(!$this -> grabHtml()) {
			return $this -> errorcode;
		}

		if($this -> existsBadwords()) {
			return $this -> errorcode;
		}

		$this->collectWebsiteData();
		$this->setWebsiteCost();

		// Begin transaction
		$transaction = Yii::app() -> db -> beginTransaction();
		try {

            Website::model()->updateByPk($wid, array('price' => $this->price, 'modified_at'=>date("Y-m-d H:i:s")));
            //WebdataAlexa::model()->updateByPk($wid, $this->alexa);
            WebdataAntivirus::model()->updateByPk($wid, $this->antivirus);
            WebdataCatalog::model()->updateByPk($wid, $this->catalog);
            WebdataLocation::model()->updateByPk($wid, $this->location);
            WebdataMetaTags::model()->updateByPk($wid, $this->metatag);
            WebdataSearchEngine::model()->updateByPk($wid, $this->searchengine);
            WebdataSocial::model()->updateByPk($wid, $this->social);
            WebdataWhois::model()->updateByPk($wid, array("text"=>$this->whois));
            if(WebdataMoz::model()->count("wid=:wid", array(":wid"=>$wid))) {
                WebdataMoz::model()->updateByPk($wid, $this->moz);
            } else {
                $moz = new WebdataMoz;
                $moz->attributes = array_merge(array("wid"=>$wid), $this->moz);
                $moz->save(false);
            }

		    $transaction->commit();
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.command.calculate.update');
			return 202;
		}
		return 0;
	}


	private function setWebsiteCost() {
		$calc = new WebsiteCostCalculator;

		$calc->setSocialActivity($this->social['facebook_total_count'] + $this->social['pins']);

		$calc->setGooglePage($this->searchengine['google_index']);
		$calc->setBingPage($this->searchengine['bing_index']);
		$calc->setYahooPage($this->searchengine['yahoo_index']);

		$calc->setPageRank(max(round(intval($this->moz['pda']) / 10), 3));
		$serpToAlexa = new SearchStatToAlexa(
            max($this->searchengine['google_index'], $this->searchengine['google_backlinks']),
            $this->searchengine['bing_index'],
            $this->searchengine['yahoo_index']
        );
		$calc->setAlexa($serpToAlexa->convert());

		$calc->setBackLinks($this->searchengine['google_backlinks']);

		$this->price = Helper::currencyPrice($calc->getPrice());
	}

	private function collectWebsiteData() {
		$alexa = SearchCatalog::alexa($this->domain);
		$alexa['data'] = @json_encode($alexa['data']);
		$this->alexa = $alexa;

		$this->antivirus['google'] = Diagnostic::google($this->url);
		$this->antivirus['avg'] = Diagnostic::norton($this->url);
		$this->catalog['dmoz'] = 0;
		$this->catalog['yahoo'] = 0;

		$this->location = Location::get($this->domain, $this->ip);

		$this->metatag["title"] = $this->Metatags->getTitle();
		$this->metatag["description"] = $this->Metatags->getDescription();
		$this->metatag["keywords"] = $this->Metatags->getKeywords();

		$this->searchengine['google_index'] = (int) SearchEngine::google($this->domain);
		$this->searchengine['bing_index'] = (int) SearchEngine::bing($this->domain);
		$this->searchengine['yahoo_index'] = (int) SearchEngine::yahoo($this->domain);
        $max = max($this->searchengine['google_index'], $this->searchengine['bing_index'], $this->searchengine['yahoo_index']);
		$this->searchengine['page_rank'] = 0;

		$backlinks = SearchEngine::googleBackLinks($this->domain);
		$this->searchengine['google_backlinks'] = Helper::are_real_backlinks(
		    $this->searchengine['google_index'],
            $this->searchengine['bing_index'],
            $this->searchengine['yahoo_index'],
            $backlinks
        ) ? $backlinks : $max;

		$moz = new Moz();
		$this->moz = $moz->get($this->domain);

		$fb = Social::facebook($this->url);
		$this->social['facebook_share_count'] = $fb['share_count'];
		$this->social['facebook_like_count'] = 0;
		$this->social['facebook_comment_count'] = $fb['comment_count'];
		$this->social['facebook_total_count'] = $fb['total_count'];
		$this->social['facebook_click_count'] = 0;
        $this->social['facebook_comment_plugin_count'] = $fb['comment_plugin_count'];
        $this->social['facebook_reaction_count'] = $fb['reaction_count'];

		$this->social['gplus'] = 0;
		$this->social['twitter'] = 0;
		$this->social['pins'] = Social::pinterest($this->url);
		$this->social['linkedin'] = 0;
		$this->social['stumbleupon'] = 0;

		$whois = new PHPWhois(
			$this->domain,
			Yii::app()->basePath.'/config/whois.servers.php',
			Yii::app()->basePath.'/config/whois.servers_charset.php',
			Yii::app()->basePath.'/config/whois.params.php'
		);
		$this->whois = $whois->query();
	}

	public function actionCheck($domain) {
		if(!$website = Website::model()->with(array(
			"alexa", "antivirus", "catalog", "location", "meta_tags", "search_engine", "social", "whois", "moz"
		))->findByAttributes(array(
			"md5domain"=>md5($domain),
		))) {
			throw new CHttpException(404, "Website $domain hasn't found in database");
		}
		$calc = new WebsiteCostCalculator;

		$calc->setSocialActivity($website->social->facebook_total_count + $website->social->pins);

		$calc->setGooglePage($website->search_engine->google_index);
		$calc->setBingPage($website->search_engine->bing_index);
		$calc->setYahooPage($website->search_engine->yahoo_index);

		$calc->setPageRank(round(intval($website->moz->pda) / 10));
        $serpToAlexa = new SearchStatToAlexa(
            max($website->search_engine->google_index, $website->search_engine->google_backlinks),
            $website->search_engine->bing_index,
            $website->search_engine->yahoo_index
        );
        $calc->setAlexa($serpToAlexa->convert());

		$calc->setBackLinks($website->search_engine->google_backlinks);

		// dollar_rate
		$this->price = Helper::currencyPrice($calc->getPrice());
		echo Helper::p($this->price)."\n";
	}

	private function existsBadwords() {
		if(!Yii::app() -> params['site_cost.validate_bad_words']) {
			return false;
		}
		$strip = trim($this->Metatags->getTitle(). " ". $this->Metatags->getDescription(). " ". $this->Metatags->getKeywords(). " ". ParseUtils::striptags($this -> html));
		$badwords_cfg = Helper::getLocalConfigIfExists("badwords");
        $badwords = implode("|", $badwords_cfg);
		$existsBadWord = preg_match_all("/\b(" . $badwords . ")\b/iu",  $strip, $matches);
		if($existsBadWord) {
			$this -> errorcode = 103;
			return true;
		}
		return false;
	}

	private function grabHtml() {
        $url = "http://".$this->domain;
        $ch = Helper::ch(curl_init($url));
        if(false === $ch) {
            $this -> errorcode = 102;
            return false;
        }

        $this->html = curl_exec($ch);
        if(curl_errno($ch)) {
            $this -> errorcode = 102;
            return false;
        }

        $info = curl_getinfo($ch);
        $this->url = Helper::url_get_scheme_host(Helper::curl_get_final_url($info, $url), $url);

		$this->Metatags = new MetaTags($this -> html);
		$charset = $this->Metatags -> getCharset();
		if(!empty($charset) and strtolower($charset) != 'utf-8') {
			$this -> html = @iconv($charset, "utf-8//IGNORE", $this -> html);
            $this->Metatags = new MetaTags($this -> html);
		}
		return true;
	}
}