<?php
class UserIdentity extends CUserIdentity {
	private $_id, $_name;

	public function authenticate() {
		$user = User::model()->findByAttributes(array(
			'email'=>$this->username,
		));
		if($user === null) {
			$this->errorMessage = Yii::t("user", "Invalid email or password");
		} else if (!Hasher::isPasswordMatched($this->password, $user->password, $user->salt)) {
			$this->errorMessage = Yii::t("user", "Invalid email or password");
		} else if (!$user->hasConfirmedEmail()) {
			$this->errorMessage = Yii::t("user", "We have sent you confirmation email. Please confirm it");
		} else if (!$user->isActive()) {
			$this->errorMessage = Yii::t("user", "User has been blocked or deleted");
		} else {
			$this->errorCode=self::ERROR_NONE;
			$this->_id = $user->id;
			$this->_name = $user->username;
		}
		return $this -> errorCode == self::ERROR_NONE;
	}

	public function getId() {
		return $this->_id;
	}

	public function getName() {
		return $this->_name;
	}
}