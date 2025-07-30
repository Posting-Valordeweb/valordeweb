<?php
class ECountryList {
	private $lang_id;
	private $countries = array();
	private static $path;
	private static $instances = array();

	private function __construct($lang_id = null) {
		$this -> lang_id = $lang_id ? substr($lang_id, 0, 2) : "en";
	}

	public static function getInstance($lang_id) {
		if(empty(self::$instances)) {
			self::$path = dirname(__FILE__) . '/countries/';
		}

		if(!isset(self::$instances[$lang_id])) {
			self::$instances[$lang_id] = new self($lang_id);
		}
		return self::$instances[$lang_id];
	}

	private function _getCountries() {
		if(!isset($this -> countries[$this -> lang_id])) {
			$file = self::$path . $this -> lang_id . '.php';
			$this -> countries[$this -> lang_id] = file_exists($file) ? include $file : include self::$path . 'en.php';
		}
		return $this -> countries[$this -> lang_id];
	}

	public function getCountries() {
		return $this -> _getCountries();
	}

	public function getSortedCountries() {
		$countries = $this -> _getCountries();
		asort($countries);
		return $countries;
	}

	public function existsCountry($country_id) {
		$countries = $this -> _getCountries();
		$id = $this->_prepareCountryID($country_id);
		return isset($countries[$id]);
	}

	public function getCountryName($country_id, $default = null) {
		$countries = $this -> _getCountries();
		$id = $this->_prepareCountryID($country_id);
		if(empty($default)) {
		    $default = strtoupper($id);
        }
		return isset($countries[$id]) ? $countries[$id] : $default;
	}

	public function getCountryIdByName($country) {
		$countries = $this -> _getCountries();
		foreach($countries as $id => $c) {
			if(mb_strtolower($c) == mb_strtolower($country)) {
				return $id;
			}
		}
		return false;
	}

	public function getCountriesStartingWithLetter($letter) {
		$sorted = $this -> getSortedCountries();
		$countries = array();
		foreach($sorted as $lang_id => $country) {
			if(mb_strtolower(mb_substr($country, 0, 1)) == mb_strtolower($letter)) {
				$countries[$lang_id] = $country;
			}
		}
		return $countries;
	}

	private function _prepareCountryID($country_id) {
		$country_id = strtoupper($country_id);
		$country_id = $country_id == 'GB' ? 'UK' : $country_id;
		return $country_id;
	}
}