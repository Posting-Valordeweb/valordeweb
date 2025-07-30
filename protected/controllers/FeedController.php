<?php
class FeedController extends FrontController {
	protected $rssPath = '';
	protected $atomPath = '';
	
	const RSS_ITEMS = 25;
	const ATOM_ITEMS = 25;
	
	public function actionRss() {
		if(!$this->existsRss() OR $this->isExpiredRss()) {
			$rss = $this->generateRss();
			$this->saveRss($rss);
		}
		$this->outputRss();
	}
	
	public function actionAtom() {
		if(!$this->existsAtom() OR $this->isExpiredAtom()) {
			$atom = $this->generateAtom();
			$this->saveAtom($atom);
		}
		$this->outputAtom();
	}
	
	protected function generateAtom() {
		$items = $this->getItemsForFeed(self::ATOM_ITEMS);
		$rfc4287string = "Y-m-d\TH:i:s\Z";
		$baseUrl = Yii::app()->getBaseUrl(true);
		$domain = $_SERVER['HTTP_HOST'];
		
		$feedUrl = $this->createAbsoluteUrl("feed/atom");
		
		$rfc4287 = date($rfc4287string);
		$document = new DOMDocument('1.0', Yii::app()->charset);
		$feedEl = $document->createElement("feed");
		$feedAttr = $document->createAttribute("xmlns");
		$feedAttr->value="http://www.w3.org/2005/Atom";
		
		$feedLangAttr = $document->createAttribute("xml:lang");
		$feedLangAttr->value = mb_substr(Yii::app()->language, 0, 2);
		
		$feedEl->appendChild($feedLangAttr);
		$feedEl->appendChild($feedAttr);
		
		$feedTitleEl = $document->createElement("title", Yii::app()->name);
		$feedEl->appendChild($feedTitleEl);

		$feedLinkEl = $document->createElement("link");
		$feedLinkHrefAttr = $document->createAttribute("href");
		$feedLinkHrefAttr->value=$feedUrl;
		$feedLinkRelAttr = $document->createAttribute("rel");
		$feedLinkRelAttr->value="self";
		$feedLinkEl->appendChild($feedLinkHrefAttr);
		$feedLinkEl->appendChild($feedLinkRelAttr);
		$feedEl->appendChild($feedLinkEl);

		$logoEl = $document->createElement("logo", Yii::app()->getBaseUrl(true)."/images/website-worth-calculator-thumb.jpg");
		$feedEl->appendChild($logoEl);
		
		$feedLinkEl = $document->createElement("link");
		$feedLinkHrefAttr = $document->createAttribute("href");
		$feedLinkHrefAttr->value=$baseUrl;
		$feedLinkEl->appendChild($feedLinkHrefAttr);
		$feedEl->appendChild($feedLinkEl);
		
		$idEl = $document->createElement("id", "tag:{$domain},".date("Y").":".md5(Yii::app()->name));
		$feedEl->appendChild($idEl);
		
		$updatedEl = $document->createElement("updated", $rfc4287);
		$feedEl->appendChild($updatedEl);

		$adminName = User::model()->findByAttributes(array("role"=>USER::ROLE_ROOT))->username;
		$adminEmail = Yii::app()->params['admin.email'];
	
		foreach($items as $item) {
			$itemEl = $document->createElement("entry");

			$url = $this->createAbsoluteUrl("website/show", array(
				"language"=>Yii::app()->language,
				"domain"=>$item->domain,
			));
			
			$itemTitleEl = $document->createElement("title", Yii::t("website", "How much {Website} is worth?", array(
				"{Website}"=>$item->idn,
			)));
			$itemEl->appendChild($itemTitleEl);
			
			$itemLinkEl = $document->createElement("link");
            $itemLinkAttr = $document->createAttribute("href");
            $itemLinkAttr->value=$url;
            $itemLinkEl->appendChild($itemLinkAttr);
			$itemEl->appendChild($itemLinkEl);
			
			$itemIdEl = $document->createElement("id", "tag:{$domain},".date("Y-m-d", strtotime($item->added_at)).":".$url);
            $itemEl->appendChild($itemIdEl);

			$itemUpdatedElRfc4287 = date($rfc4287string, strtotime($item->added_at));
			$itemUpdatedEl = $document->createElement("updated", $itemUpdatedElRfc4287);
			$itemEl->appendChild($itemUpdatedEl);
			
			$thumbnail=WebsiteThumbnail::getOgImage(array(
				'url'=>$item->domain,
				'size'=>'l',
			));
			
			$atomHTML = $this->renderPartial("rss", array(
				"item"=>$item,
				"thumbnail"=>$thumbnail,
				"url"=>$url
			), true);


			$itemContentEl = $document->createElement("content");
			$itemContentAttr = $document->createAttribute("type");
			$itemContentAttr->value="xhtml";
			$itemContentEl->appendChild($itemContentAttr);

            $itemContentDivEl = $document->createElement("div");
            $itemContentDivElAttr = $document->createAttribute("xmlns");
            $itemContentDivElAttr->value="http://www.w3.org/1999/xhtml";
            $itemContentDivEl->appendChild($itemContentDivElAttr);

            $fragmentEl = $document->createDocumentFragment();
            $fragmentEl->appendXML($atomHTML);
            $itemContentDivEl->appendChild($fragmentEl);

            $itemContentEl->appendChild($itemContentDivEl);

            $itemEl->appendChild($itemContentEl);
			
			$itemAuthorEl = $document->createElement("author");
			$itemAuthorNameEl = $document->createElement("name", $adminName);
			$itemAuthorEmailEl = $document->createElement("email", $adminEmail);
			$itemAuthorEl->appendChild($itemAuthorNameEl);
			$itemAuthorEl->appendChild($itemAuthorEmailEl);
			$itemEl->appendChild($itemAuthorEl);
			
			$feedEl->appendChild($itemEl);
		}
		
		$document->appendChild($feedEl);
		return $document->saveXML();
	}
	
	protected function generateRss() {
		$items = $this->getItemsForFeed(self::RSS_ITEMS);
		$rfc822string = "D, d M Y H:i:s O";
	
		$rfc822 = date($rfc822string);
		
		$document = new DOMDocument('1.0', Yii::app()->charset);
		$rss = $document->createElement("rss");
		$rssVersionAttr = $document->createAttribute("version");
		$rssVersionAttr->value="2.0";
		$rss->appendChild($rssVersionAttr);
		
		$document->appendChild($rss);
		
		$channel = $document->createElement("channel");
		
		$titleEl = $document->createElement("title", Yii::app()->name);
		$channel->appendChild($titleEl);
		
		$descEl = $document->createElement("description", Yii::t("website", "Estimated website cost of any domain"));
		$channel->appendChild($descEl);
		
		$linkEl = $document->createElement("link", Yii::app()->getBaseUrl(true));
		$channel->appendChild($linkEl);
		
		$languageEl = $document->createElement("language", mb_substr(Yii::app()->language, 0, 2));
		$channel->appendChild($languageEl);
		
		$imageEl = $document->createElement("image");
		$imageUrlEl = $document->createElement("url", Yii::app()->getBaseUrl(true)."/images/website-worth-calculator-thumb.jpg");
		$imageTitleEl = $document->createElement("title", Yii::app()->name);
		$imageLinkEl = $document->createElement("link", Yii::app()->getBaseUrl(true));
		$imageEl->appendChild($imageUrlEl);
		$imageEl->appendChild($imageTitleEl);
		$imageEl->appendChild($imageLinkEl);
		
		$channel->appendChild($imageEl);
		
		$pubDateEl = $document->createElement("pubDate", $rfc822);
		$channel->appendChild($pubDateEl);
		
		$adminName = User::model()->findByAttributes(array("role"=>USER::ROLE_ROOT))->username;
		$adminEmail = Yii::app()->params['admin.email'];
		
		foreach($items as $item) {
			$itemEl = $document->createElement("item");
			$itemTitleEl = $document->createElement("title", Yii::t("website", "How much {Website} is worth?", array(
				"{Website}"=>$item->idn,
			)));
			$itemEl->appendChild($itemTitleEl);
			
			$url = $this->createAbsoluteUrl("website/show", array(
				"language"=>Yii::app()->language,
				"domain"=>$item->domain,
			));
			
			$itemLinkEl = $document->createElement("link", $url);
			$itemEl->appendChild($itemLinkEl);
			
			$itemGuidEl = $document->createElement("guid", $url);
			$itemEl->appendChild($itemGuidEl);
			
			$itemPubDateRfc822 = date($rfc822string, strtotime($item->added_at));
			$itemPubDateEl = $document->createElement("pubDate", $itemPubDateRfc822);
			$itemEl->appendChild($itemPubDateEl);
			
			$itemAuthorEl = $document->createElement("author", sprintf("%s (%s)", $adminEmail, $adminName));
			$itemEl->appendChild($itemAuthorEl);
			
			$thumbnail=WebsiteThumbnail::getOgImage(array(
				'url'=>$item->domain,
				'size'=>'l',
			));
			
			$descriptionHTML = $this->renderPartial("rss", array(
				"item"=>$item,
				"thumbnail"=>$thumbnail,
				"url"=>$url
			), true);
			
			$itemDescriptionEl = $document->createElement("description", $descriptionHTML);
			$itemEl->appendChild($itemDescriptionEl);

			$channel->appendChild($itemEl);
		}
		
		$rss->appendChild($channel);
		return $document->saveXML();
	}
	
	protected function getLastWebsiteDate() {
		return (int) strtotime(Yii::app()->db->createCommand()
			-> select("max(added_at)")
			-> from("{{webdata_main}}")
			-> queryScalar());
	}
	
	protected function getItemsForFeed($items) {
		$criteria = new CDbCriteria();
		$criteria -> select = "t.idn, t.domain, t.added_at, t.price";
		$criteria -> with = array(
			"search_engine" => array(
				"select"=>"google_index",
			),
			"alexa" => array(
				"select"=>"rank",
			),
			"social" => array(
				"select"=>"facebook_total_count",
			),
            "antivirus"=>array(
                "select"=>"avg"
            )
		);
		$criteria -> order = "t.added_at desc";
		$criteria -> limit = $items;
		return Website::model()->findAll($criteria);
	}
	
	// Path
	protected function getRssPath() {
		if(empty($this->rssPath)) {
			$this->rssPath = $this->getFeedPath("rss");
		}
		return $this->rssPath;
	}
	
	protected function getAtomPath() {
		if(empty($this->atomPath)) {
			$this->atomPath = $this->getFeedPath("atom");
		}
		return $this->atomPath;
	}
	
	protected function getFeedPath($type) {
		return $this->rssPath = Yii::app()->runtimePath.'/feed/'.Yii::app()->language."_{$type}.xml";
	}
	// End of Path
	
	
	// Save
	protected function saveRss($string) {
	    $path = $this->getRssPath();
        $res = file_put_contents($this->getRssPath(), $string);
        @chmod($path,0666);
        return $res;
	}
	
	protected function saveAtom($string) {
	    $path = $this->getAtomPath();
        $res = file_put_contents($this->getAtomPath(), $string);
        @chmod($path,0666);
        return $res;
	}
	// End of save
	
	
	// Output
	protected function outputRss() {
		header("Content-Type: application/rss+xml; charset=".Yii::app()->charset);
		echo file_get_contents($this->getRssPath());
		exit(0);
	}
	
	protected function outputAtom() {
		header("Content-Type: application/atom+xml; charset=".Yii::app()->charset);
		echo file_get_contents($this->getAtomPath());
		exit(0);
	}
	// End of output
	
	
	// Feed existing
	protected function existsRss() {
		return file_exists($this->getRssPath());
	}
	protected function existsAtom() {
		return file_exists($this->getAtomPath());
	}
	// End of feed existing
	
	
	// Mofidied
	protected function getLastRssModified() {
		return filemtime($this->getRssPath());
	}
	
	protected function getLastAtomModified() {
		return filemtime($this->getAtomPath());
	}
	// End of modified
	
	// Is expired feed
	protected function isExpiredRss() {
		return $this->getLastWebsiteDate() > $this->getLastRssModified();
	}
	protected function isExpiredAtom() {
		return $this->getLastWebsiteDate() > $this->getLastAtomModified();
	}
}