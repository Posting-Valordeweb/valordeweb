<?php
class LoginForm extends CFormModel {
	public $email, $password, $verifyCode, $remember;
	private $_identity;

	public function rules() {
        $rules = array();
        $rules[] = array('email, password', 'required');
        $rules[] = array('email', 'length', 'max' => 80);
        if(Helper::isAllowedCaptcha()) {
            $rules[] = array('verifyCode', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>Yii::app()->params['recaptcha.private'], 'message'=>Yii::t("yii", "The verification code is incorrect."));
        }
        $rules[] = array('remember', 'safe');
        $rules[] = array('email', 'authenticate');
        return $rules;
	}

	public function authenticate() {
		if($this -> hasErrors()) {
			return false;
		}
		$this->_identity = new UserIdentity($this->email, $this->password);
		if($this->_identity->authenticate()) {
			$duration = $this->remember ? 60 * 60 * 24 * 30 : 0;
			Yii::app()->user->login($this->_identity, $duration);
		} else {
			$this -> addError("email", $this->_identity->errorMessage);
		}
	}

	public function attributeLabels() {
		return array(
			"email"=>Yii::t("user", "Email"),
			"password"=>Yii::t("user", "Password"),
			"verifyCode"=>Yii::t("user", "Verification code"),
			"remember"=>Yii::t("user", "Remember me"),
		);
	}
}