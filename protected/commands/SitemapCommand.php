<?php
set_time_limit(0);
/*
101: Sitemap directory or sitemap file has no write permissions
*/

class SitemapCommand extends CConsoleCommand {
    protected $urlCount = 50000;
    protected $sitemapDir;
    protected $sitemapFile;

    public function init() {
        $this->sitemapDir = Yii::app()->basePath . '/../sitemap/';
        $this->sitemapFile = Yii::app()->basePath. '/../sitemap.xml';
    }

    public function actionIndex() {
        if(!is_writable($this->sitemapDir) OR !is_writable($this->sitemapFile)) {
            Yii::log("Make sure {$this->sitemapDir} and {$this->sitemapFile} is writeable", 'error', 'application.command.sitemap.index');
            return 101;
        }
        $languages = Language::model()->getList();

        if(!Yii::app()->params['url.multi_language_links']) {
            $lang = Yii::app()->language;
            if (isset($languages[$lang])) {
                $defaultLanguage = $languages[$lang];
            } else {
                $defaultLanguage = new Language();
                $defaultLanguage->id = Yii::app()->language;
                $defaultLanguage->language = Yii::app()->language;
                $defaultLanguage->enabled = Language::STATUS_DISABLED;
                $defaultLanguage->is_default = Language::DEFAULT_LANG;
            }
            $languages = array($lang => $defaultLanguage);
        }


        $date = date('c', time());
        $this->cleanSitemapDirectory();

        $baseUrl = Yii::app()->getRequest()->getBaseUrl(true);

        Yii::app()->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $dataReader = Yii::app()->db->createCommand()
            ->select('domain, modified_at')
            ->from('{{webdata_main}}')
            ->query();

        $sitemapCounter = $urlCounter = 0;
        $sitemapIndexDOM = $this->createSitemapIndexDOM();

        $sitemapDOM = $this->createSitemapDOM();
        $sitemapDoc = $sitemapDOM["document"];
        $sitemapUrlset = $sitemapDOM["urlset"];

        while(($website=$dataReader->read())!==false) {
            foreach($languages as $id => $language) {
                $link = Yii::app()->createAbsoluteUrl("website/show", array(
                        "domain" => CHtml::encode($website['domain']),
                        "language" => $id,
                    ));

                $lastmod = date('c', strtotime($website['modified_at']));
                $urlDOM = $sitemapDoc->createElement('url');
                $urlDOM->appendChild($sitemapDoc->createElement('loc'))->appendChild($sitemapDoc->createTextNode($link));
                $urlDOM->appendChild($sitemapDoc->createElement('lastmod'))->appendChild($sitemapDoc->createTextNode($lastmod));
                $urlDOM->appendChild($sitemapDoc->createElement('changefreq'))->appendChild($sitemapDoc->createTextNode('daily'));
                $sitemapUrlset->appendChild($urlDOM);
                $urlCounter++;
                if($urlCounter == $this->urlCount) {
                    if($this->saveGz($sitemapCounter, $sitemapDoc->saveXML())) {
                        $sitemap = $sitemapIndexDOM["document"]->createElement('sitemap');
                        $sitemapURL = $baseUrl."/sitemap/sitemap$sitemapCounter.xml.gz";
                        $sitemap->appendChild($sitemapIndexDOM["document"]->createElement('loc'))->appendChild($sitemapIndexDOM["document"]->createTextNode($sitemapURL));
                        $sitemap->appendChild($sitemapIndexDOM["document"]->createElement('lastmod'))->appendChild($sitemapIndexDOM["document"]->createTextNode($date));
                        $sitemapIndexDOM["index"] -> appendChild($sitemap);
                    }

                    $sitemapDOM = $this->createSitemapDOM();
                    $sitemapDoc = $sitemapDOM["document"];
                    $sitemapUrlset = $sitemapDOM["urlset"];

                    $sitemapCounter++;
                    $urlCounter = 0;
                }
            }
        }
        if($sitemapUrlset->hasChildNodes()) {
            if($this->saveGz($sitemapCounter, $sitemapDoc->saveXML())) {
                $sitemap = $sitemapIndexDOM["document"]->createElement('sitemap');
                $sitemapURL = $baseUrl."/sitemap/sitemap$sitemapCounter.xml.gz";
                $sitemap->appendChild($sitemapIndexDOM["document"]->createElement('loc'))->appendChild($sitemapIndexDOM["document"]->createTextNode($sitemapURL));
                $sitemap->appendChild($sitemapIndexDOM["document"]->createElement('lastmod'))->appendChild($sitemapIndexDOM["document"]->createTextNode($date));
                $sitemapIndexDOM["index"] -> appendChild($sitemap);
            }
        }
        file_put_contents(Yii::app() -> basePath . "/../sitemap.xml", $sitemapIndexDOM["document"]->saveXML(), LOCK_EX);
        return 0;
    }

    protected function saveGz($sitemapNr, $sitemapString) {
        $sitemap = $this->sitemapDir."sitemap$sitemapNr.xml.gz";
        if(!$fp = gzopen($sitemap, 'w9')) {
            return false;
        }
        gzwrite($fp, $sitemapString);
        gzclose($fp);
        @chmod($sitemap, 0666);
        return true;
    }

    protected function createSitemapIndexDOM() {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $sitemapIndex = $xml->createElement('sitemapindex');
        $attrNode = $xml->createAttribute('xmlns');
        $attrNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $xml->appendChild($sitemapIndex);
        $sitemapIndex->appendChild($attrNode);
        return array(
            "document" => $xml,
            "index" => $sitemapIndex,
        );
    }

    protected function createSitemapDOM() {
        $document = new DOMDocument('1.0', 'UTF-8');
        $urlsetNode = $document->createElement('urlset');
        $xmlnsNode = $document->createAttribute('xmlns');
        $xmlnsNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $document->appendChild($urlsetNode);
        $urlsetNode->appendChild($xmlnsNode);
        return array(
            "document" => $document,
            "urlset" => $urlsetNode,
        );
    }

    protected function cleanSitemapDirectory() {
        if(!$files = glob($this->sitemapDir.'*')) {
            return true;
        }
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
    }
}