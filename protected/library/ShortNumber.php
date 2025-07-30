<?php
class ShortNumber {
	public function getShortPrice($price) {
		if($price < 0) {
			return 0;
		}
		$tmp=$price;
		$i=0;
		while($tmp>=10) {
			$tmp=round($tmp/10);
			$i++;
		}
		$mod=pow(10,$i-($i%3));
		return sprintf("%01.2f %s", $price/$mod, $this->getShortWordByZeroCount($i));
	}

	protected function getShortWordByZeroCount($cnt) {
		if($cnt<3) {
			return null;
		} else if ($cnt>=3 AND $cnt<=5) {
			return "K";
		} else if($cnt>=6 AND $cnt<=8) {
			return "MIL";
		} else if($cnt>=9 AND $cnt<=11) {
			return "BIL";
		} else if($cnt>=12 AND $cnt<=14) {
			return "TRL";
		} else {
			return "o.O";
		}
	}
}