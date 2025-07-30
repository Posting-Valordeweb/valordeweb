<?php
class ContactForm extends CFormModel {
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;

	public function rules() {
	    $rules = array();
	    $rules[] = array('name, email, subject, body', 'required');
        $rules[] = array('email', 'email');
        if(Helper::isAllowedCaptcha()) {
            $rules[] = array('verifyCode', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>Yii::app()->params['recaptcha.private'], 'message'=>Yii::t("yii", "The verification code is incorrect."));
        }
        return $rules;
	}

	public function attributeLabels() {
		return array(
			'name' => Yii::t("contact", "Name"),
			'email' => Yii::t("contact", "Email"),
			'subject' => Yii::t("contact", "Subject"),
			'body' => Yii::t("contact", "Body"),
			"verifyCode"=>Yii::t("misc", "Verification code"),
		);
	}
}