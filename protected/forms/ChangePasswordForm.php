<?php
class ChangePasswordForm extends CFormModel {
	public $oldpassword, $password, $password2;

	public function rules() {
		return array(
			array('password, password2', 'required'),
			array('oldpassword', 'required', 'on'=>'manualChange'),
			array('password', 'length', 'min' => 5),
			array('password2', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t("user", "Passwords do not match")),

			array('oldpassword', 'matchold', 'on'=>'manualChange'),
		);
	}
	public function attributeLabels() {
		return array(
			'password' => Yii::t("user", "Password"),
			'password2' => Yii::t("user", "Re-Password"),
			'oldpassword' => Yii::t("user", "Old password"),
		);
	}
	public function matchOld() {
		if($this -> hasErrors()) {
			return false;
		}
		$user = Yii::app() -> user -> loadModel();

		if(!Hasher::isPasswordMatched($this->oldpassword, $user->password, $user->salt)) {
			$this->addError("oldpassword", Yii::t("user", "Incorrect old password"));
		}
	}
}