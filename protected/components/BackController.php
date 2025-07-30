<?php
class BackController extends BaseController {
	protected $_end = 'back';
	public $user;

	public function init() {
		parent::init();
		$this->user=Yii::app()->user->loadModel();
	}

	protected function setUserLoginUrl() {
		Yii::app()->user->loginUrl = $this->createUrl('admin/user/login');
	}

	protected function setErrorHandlerAction() {
		Yii::app()->errorHandler->errorAction='admin/site/error';
	}
}