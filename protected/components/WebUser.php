<?php
class WebUser extends CWebUser {
	protected $_model = null;

	public function init() {
		parent::init();

		if($this->isGuest) {
			return;
		}

		$user = $this->loadModel();
		$this->validate();

		if($user->lang_id != Yii::app()->language) {
			User::model()->updateByPk($this->id, array(
				"lang_id"=>Yii::app()->language,
			));
		}
	}

	public function isSuperUser() {
		return $this->loadModel()->isSuperUser();
	}

	public function appendFlash($type, $message, $break = '<br/>') {
		$m = $this->getFlash($type);
		!empty($m) ? $this->setFlash($type, $m.$break.$message) : $this->setFlash($type, $message);
	}

	public function getRole() {
		if($user = $this->loadModel()) {
			return $user->role;
		}
	}

	protected function afterLogin($fromCookie) {
		User::model()->updateByPk($this->id, array(
			"last_ip_login"=>Yii::app()->request->getUserHostAddress(),
			"last_login_at"=>date("Y-m-d H:i:s"),
		));
	}

    public function loadModel() {
		if(!$this->isGuest AND $this->_model === null) {
			$this->_model = User::model()->findByPk($this->id);
		}
		return $this->_model;
	}

	private function validate()
    {
        if(
            (is_null($this->_model)) ||
            (!$this->_model->isActive())
        ) {
            $this->logout();
            Yii::app()->request->redirect(Yii::app()->getHomeUrl());
        }
    }
}