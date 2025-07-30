<?php
class WebsiteCostCalculator {
	private $pageRates = array(
		'google' => 0.15,
		'bing' => 0.10,
		'yahoo' => 0.08
	);
	private $socialRate = 0.7;

	private $prRates = array(
		0 => 0.5,
		1 => 0.7,
		2 => 1,
		3 => 1.05,
		4 => 1.15,
		5 => 1.3,
		6 => 3.0,
		7 => 9,
		8 => 10,
		9 => 10.5,
		10 => 11,
	);

	private $bonusBasedOnAlexa = array(
		10 => array(
			'alexa'=>1.30,
			'backlinks'=>1,
		),
		50 => array(
			'alexa'=>1.26,
			'backlinks'=>0.95,
		),
		100 => array(
			'alexa' => 1.25,
			'backlinks'=>0.90,
		),
		500 => array(
			'alexa' => 1.23,
			'backlinks'=>0.85,
		),
		1000 => array(
			'alexa' => 1.21,
			'backlinks'=>0.80,
		),
		10000 => array(
			'alexa' => 1.20,
			'backlinks'=>0.75,
		),
		50000 => array(
			'alexa' => 1.19,
			'backlinks'=>0.70,
		),
		100000 => array(
			'alexa' => 1.18,
			'backlinks'=>0.65,
		),
		200000 => array(
			'alexa' => 1.17,
			'backlinks'=>0.60,
		),
		350000 => array(
			'alexa' => 1.16,
			'backlinks'=>0.55,
		),
		400000 => array(
			'alexa' => 1.15,
			'backlinks'=>0.50,
		),
		600000 => array(
			'alexa' => 1.14,
			'backlinks'=>0.45,
		),
		900000 => array(
			'alexa' => 1.13,
			'backlinks'=>0.40,
		),
		1200000 => array(
			'alexa' => 1.12,
			'backlinks'=>0.30,
		),
		3000000 => array(
			'alexa' => 1.11,
			'backlinks'=>0.25,
		),
		5000000 => array(
			'alexa' => 1.09,
			'backlinks'=>0.15,
		),
		10000000 => array(
			'alexa' => 1.07,
			'backlinks'=>0.05,
		),
		15000000 => array(
			'alexa' => 1.05,
			'backlinks'=>0.03,
		),
        20000000 => array(
            'alexa' => 1.03,
            'backlinks'=>0.03,
        ),
        30000000 => array(
            'alexa' => 1.02,
            'backlinks'=>0.02,
        ),
	);

	private $alexaTotal = 35000001;
	private $socialActivity, $googlePage = 0, $bingPage = 0, $yahooPage = 0, $pageRank = 0, $alexa = 0,
	$backLinks = 0;

	public function setSocialActivity($socialActivity) { $this->socialActivity = $this->abs($socialActivity); }

	public function setGooglePage($n) { $this->googlePage = $this->abs($n); }
	public function setBingPage($n) { $this->bingPage = $this->abs($n); }
	public function setYahooPage($n) { $this->yahooPage = $this->abs($n); }

	public function setPageRank($pr) { $this->pageRank = $this->abs($pr); }

	public function setBackLinks($n) { $this->backLinks = $this->abs($n); }

	public function setAlexa($n) { $this->alexa = $this->abs($n); }

	private function getPrRate() {
		// PageRank has been closed, so we need convert alexa rank to PR approximately.
		$this->pageRank = $this->convertAlexaToPageRank();
		return isset($this->prRates[$this->pageRank]) ? $this->prRates[$this->pageRank] : 0.1;
	}

	private function convertAlexaToPageRank() {
		$conv = array(
			10=>10,
			9=>50,
			8=>500,
			7=>1000,
			6=>5000,
			5=>10000,
			4=>30000,
			3=>60000,
			2=>120000,
			1=>500000,
			0=>1000000,
		);
		$na = 'n-a';
		if(!$this->alexa) {
			return $na;
		}
		foreach($conv as $pr=>$alexaRank) {
			if($this->alexa <= $alexaRank) {
				return $pr;
			}
		}
		return $na;
	}

	private function getAlexaBonus() {
		if($this->alexa <= 0) {
			return 0;
		}
		$bonus = $this->getBonusBasedOnAlexa('alexa');
		$diff = $this->alexaTotal - $this->alexa;
		if($diff < 0) {
			$diff = 0;
		}
		return (1 / $this->alexa) * pow($diff, $bonus['const']);
	}

	private function getBonusBasedOnAlexa($type) {
		if($this->alexa <= 0) {
			$last = array_slice($this->bonusBasedOnAlexa, -1, 1, true);
			foreach($last as $id=>$const) {
				return array(
					'id'=>$id, 'const'=>$const[$type]
				);
			}
		}
		foreach($this->bonusBasedOnAlexa as $id => $const) {
			if($this->alexa < $id) {
				break;
			}
		}
		return array('id'=>$id, 'const'=>$const[$type]);
	}


	private function getSearchEngineBonus() {
		$prRate = $this->getPrRate();
		$bonus = 	($this->pageRates['google'] * $prRate * $this->googlePage) +
							($this->pageRates['bing'] * $prRate * $this->bingPage) +
							($this->pageRates['yahoo'] * $prRate * $this->yahooPage);
		return $bonus;
	}

	private function getSocialBonus() {
		return $this->socialActivity * $this->socialRate * 2;
	}

	private function getBackLinksBonus() {
		$bonus = $this->getBonusBasedOnAlexa('backlinks');
		return $this->backLinks * $bonus['const'];
	}

	private function getDomainBonus() {
	    return 25;
    }

	public function getPrice() {
		$socialBonus = $this->getSocialBonus();
		$searchBonus = $this->getSearchEngineBonus();
		$alexaBonus = $this->getAlexaBonus();
		$backLinksBonus = $this->getBackLinksBonus();
		$domainBonus = $this->getDomainBonus();
		return $socialBonus + $searchBonus + $alexaBonus + $backLinksBonus + $domainBonus;
	}

	public function flush() {
		$this->fbLike=$this->gPlus=$this->tweet=$this->googlePage=
		$this->bingPage=$this->yahooPage=$this->pageRank=$this->alexa=$this->backLinks=0;
	}

	private function abs($n, $d=0) { return (int) $n < 0 ? $d : (int) $n; }
}