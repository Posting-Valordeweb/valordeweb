<?php
class EAlphabet {
	private $lang_id;
	private static $alphabet = array();
	private static $instances = array();

	private function __construct($lang_id = null) {
		$this -> lang_id = $lang_id ? substr($lang_id, 0, 2) : "en";
		$this->checkIfExistsAlphabet();
	}

	public static function getInstance($lang_id) {
		static $included = false;
		if(!$included) {
			self::$alphabet = include (dirname(__FILE__).'/alphabet.php');
			$included = true;
		}
		if(!isset(self::$instances[$lang_id])) {
			self::$instances[$lang_id] = new self($lang_id);
		}
		return self::$instances[$lang_id];
	}

	private function checkIfExistsAlphabet() {
		if(!isset(self::$alphabet[$this->lang_id])) {
			throw new Exception("The alphabet for lang {$this->lang_id} doesn't exists");
		}
	}

	public function getFirstLetter() {
		return mb_substr($this->getAlphabet(), 0, 1);
	}

	public function issetLetter($letter) {
		return mb_strpos($this->getAlphabet(), $letter);
	}

	public function getAlphabet() {
		return self::$alphabet[$this->lang_id];
	}

	public function getAlphabetAsArray($l=0) {
		$str = $this->getAlphabet();
		if($l > 0) {
			$ret = array();
			$len = mb_strlen($str);
			for ($i = 0; $i < $len; $i += $l) {
				$ret[] = mb_substr($str, $i, $l);
			}
			return $ret;
		}
		return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	}
}