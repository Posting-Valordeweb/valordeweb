<?php
class CalculationForm extends CFormModel {
	public $domain; // xn-domain
	public $ip;
	public $idn; // президент.рф (IDN)
	public $verifyCode;
	private $_website;

	public function rules() {
	    $rules = array();
	    $rules[] = array('domain', 'filter', 'filter'=>array('Helper', 'trimDomain'));
	    $rules[] = array('domain', 'filter', 'filter'=>array($this, 'punycode'));
	    $rules[] = array('domain', 'required');
	    if(Helper::isAllowedCaptcha()) {
            $rules[] = array('verifyCode', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>Yii::app()->params['recaptcha.private'], 'message'=>Yii::t("yii", "The verification code is incorrect."));
        }
        $rules[] = array('domain', 'isReachable');
        $rules[] = array('domain', 'bannedWebsites');
        $rules[] = array('domain', 'tryToAnalyse');

        return $rules;
	}

	public function punycode($domain) {
		$this->domain = idn_to_ascii($domain);
		$this->idn = idn_to_utf8($domain);
		return $this->domain;
	}

	public function bannedWebsites() {
		if(!$this -> hasErrors()) {
			$banned = Helper::getLocalConfigIfExists("domain_restriction");
            foreach($banned as $pattern) {
                if(preg_match("#{$pattern}#i", $this->idn)) {
                    $this -> addError("domain", Yii::t("error_code", "Calculation Error Code 103"));
                }
            }
		}
	}

	public function attributeLabels() {
		return array(
			'domain' => Yii::t("website", "Domain name"),
		);
	}

	public function isReachable() {
		if(!$this -> hasErrors()) {
			$this -> ip = gethostbyname($this -> domain);
			$long = ip2long($this -> ip);
			if($long == -1 OR $long === FALSE) {
				$this->addError("domain", Yii::t("website", "Could not reach host: {Host}", array("{Host}" => $this -> domain)));
			}
		}
	}
	
	public function getWebsite() {
		return $this->_website;
	}

	public function tryToAnalyse() {
		if(!$this -> hasErrors()) {
			$this->loadWebsite();
            if($this->_website AND ($notUpd = (strtotime($this->_website->modified_at) + Yii::app() -> params["site_cost.cache"] > time()))) {
				return true;
			} elseif($this->_website AND !$notUpd) {
				$args = array('yiic', 'calculate', 'update',
					"--domain={$this->domain}",
					"--idn={$this->idn}",
					"--ip={$this->ip}",
					"--wid={$this->_website->id}"
				);
			} else {
				$args = array('yiic', 'calculate', 'insert',
					"--domain={$this->domain}",
					"--idn={$this->idn}",
					"--ip={$this->ip}"
				);
			}

			// Get command path
			$commandPath = Yii::app() -> getBasePath() . DIRECTORY_SEPARATOR . 'commands';

			// Create new console command runner
			$runner = new CConsoleCommandRunner();

			// Adding commands
			$runner -> addCommands($commandPath);

			// If something goes wrong return error
			if($error = $runner -> run ($args)) {
				$this -> addError("domain", Yii::t("error_code", "Calculation Error Code $error"));
			} else {
				$this->loadWebsite();
				return true;
			}
		}
	}

	protected function loadWebsite() {
		if(!empty($this->_website)) {
			return;
		}
		$this->_website = Website::model()->findByAttributes(array(
			'md5domain'=>md5($this->domain),
		));
	}
}