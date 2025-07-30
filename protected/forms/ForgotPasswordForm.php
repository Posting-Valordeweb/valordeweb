<?php
class ForgotPasswordForm extends CFormModel {
	public $email;
	public $verifyCode;

	public function rules() {
	    $rules = array();
        $rules[] = array('email', 'required');
        $rules[] = array('email', 'email');
        if(Helper::isAllowedCaptcha()) {
            $rules[] = array('verifyCode', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>Yii::app()->params['recaptcha.private'], 'message'=>Yii::t("yii", "The verification code is incorrect."));
        }
        $rules[] = array('email', 'exist', 'className' => 'User', 'attributeName' => 'email', 'message' => Yii::t("user", "We didn't find such email address"), 'criteria'=>array(
            'condition'=>'role=:role',
            'params'=>array(':role'=>User::ROLE_USER),
        ));
        return $rules;
	}
	public function attributeLabels() {
		return array(
			'email' => Yii::t("user", "Email"),
            "verifyCode"=>Yii::t("user", "Verification code"),
		);
	}
}