<?php
/*
 * $finder = new LinkFinder();
 * $finder->setUrl('http://tts.lt');
 * $finder->setHref('http://php5developer.com');
 * $finder->exists();
 *
 * $finder = new LinkFinder();
 * $finder->setHref('http://php5developer.com');
 * foreach($urls as $url) {
 *  $finder->setUrl($url);
 *  if(!$finder->exists()) {
 *      echo 'achtung';
 *  }
 * }
 */

class LinkFinder {
    private $url;
    private $href;
    private $allowNoFollow=false;
    private $like=true;

    public function setUrl($url) {
        $this->url=trim($url);
    }
    public function setHref($href) {
        $this->href=trim($href);
    }
    public function allowNoFollow($boolean) {
        $this->allowNoFollow=$boolean;
    }
    public function setLike($boolean) {
			$this->like=$boolean;
    }

    public function exists() {
				if(!$html=$this->getHtml()) {
						return false;
				}
				$links=$this->getLinks($html);
				$endPattern=!$this->like ? "$" : null;
				foreach($links[2] as $id=>$href) {
						if(preg_match("#^(https?://)?{$this->href}{$endPattern}#i", $href, $matches)) {
							return $this->allowNoFollow==false ? $this->isDofollowLink($links[0][$id]) : true;
						}
				}
				return false;
    }

    private function getHtml() {
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $html = Helper::curl_exec($curl);
        return $html == false ? false : $html;
    }

    private function getLinks($html) {
        $pattern = "#<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>#siU";
        preg_match_all($pattern, $html, $matches);
        // $matches[0] - raw <a> text
        // $matches[1] - quote type (ex: ", ')
        // $matches[2] - url
        // $matches[3] - anchor
        return $matches;
    }

    private function isDofollowLink($a_tag) {
        $pattern = "#rel=(?:'([^']+)'|\"([^\"]+)\")#is";
        preg_match($pattern, $a_tag, $rel);
        if(empty($rel)) {
            return true;
        }
        $juice = strtolower(trim($rel[2], '"'));
        return 'dofollow'==$juice;
    }
}