<?php
class Estimator {
	private $alexa = 0;
	
	const AVERAGE_DAYS_IN_MONTH = 29.53;
    private static $powerCoefficients = array(
        25 => 0.3,
        50 => 0.1,
        100 => 0.05,
        1000 => -0.500,
        10000 => -0.550,
        50000 => -0.600,
        100000 => -0.650,
        500000 => -0.720,
        1000000 => -0.830,
        5000000 => -0.860
    );
    private static $leastCoefficient = -0.9;

    private $power;

	const MAX_UNIQUE_VISITORS_PER_DAY = 15000000;
	const ESTIMATE_PAGEVIEWS = 3;
	const PERCENT_OF_PAGEVIEWS = 0.1; // 10 %
	const AD_UNIT_CPM = 0.03;
	const AVERAGE_DAYS_IN_YEAR = 365.2425;

    private $uv = null;
    private $dr = null;
    private $pw = null;

	public function __construct($alexaRank) {
		$this->alexa = (int) $alexaRank;
        $this->power = $this->calculatePowerCoefficients();
	}

    private function calculatePowerCoefficients() {
        foreach (self::$powerCoefficients as $rank => $coefficient) {
            if ($this->alexa < $rank) {
                return $coefficient;
            }
        }
        return self::$leastCoefficient;
    }
	
	protected function _getEstimatedUniqueDailyVisitors() {
		if(null === $this->uv) {
            $this->uv = self::MAX_UNIQUE_VISITORS_PER_DAY * pow($this->alexa, $this->power);
		}
		return $this->uv;
	}
	
	protected function _getEstimatedUniqueMonthlyVisitors() {
		return $this->getEstimatedUniqueDailyVisitors() * self::AVERAGE_DAYS_IN_MONTH;
	}
	
	protected function _getEstimatedUniqueYearlyVisitors() {
		return $this->getEstimatedUniqueDailyVisitors() * self::AVERAGE_DAYS_IN_YEAR;
	}
	
	protected function _getEstimatedDailyAdsRevenue() {
		if(null===$this->dr) {
			$this->dr = $this->getPercentOfPageViews() * self::AD_UNIT_CPM;
		}
		return $this->dr;
	}
	
	protected function _getEstimatedMonthlyAdsRevenue() {
		return $this->getEstimatedDailyAdsRevenue() * self::AVERAGE_DAYS_IN_MONTH;
	}

	protected function _getEstimatedYearlyAdsRevenue() {
		return $this->getEstimatedDailyAdsRevenue() * self::AVERAGE_DAYS_IN_YEAR;
	}

	protected function _getEstimatedDailyPageViews() {
		if(null===$this->pw) {
			$this->pw = round($this->getEstimatedUniqueDailyVisitors() * self::ESTIMATE_PAGEVIEWS);
		}
		return $this->pw;
	}

	protected function _getEstimatedMonthlyPageViews() {
		return $this->getEstimatedDailyPageViews() * self::AVERAGE_DAYS_IN_MONTH;
	}
	
	protected function _getEstimatedYearlyPageViews() {
		return $this->getEstimatedDailyPageViews() * self::AVERAGE_DAYS_IN_YEAR;
	}

	private function getPercentOfPageViews() {
		return $this->getEstimatedDailyPageViews() * self::PERCENT_OF_PAGEVIEWS;
	}
	
	public function __call($method, $args) {
		$method = "_".$method;
		if(method_exists($this, $method)) {
			if($this->alexa <= 0) {
				return 0;
			}
			return call_user_func_array(array($this, $method), $args);
		}
		throw new Exception ("Unable to find {$method} in Estimator");
	}
}