<?php
class AdminEditableController extends FrontController {
	public $owner;
	public $invisible=false;
	public $warningTitle;

	public function init() {
		parent::init();
		$owner=Yii::app()->request->getQuery('owner');
		$user=Yii::app()->user->loadModel();
		if($owner AND $user->isSuperUser()) {
			if(!$this->owner=User::model()->findByPk($owner)) {
				throw new CHttpException(404, Yii::t("notification", "Unable to find user in database"));
			}
			$this->invisible=true;
			$this->layout="/{$this->_end}/layouts/admin_editable";
			$this->warningTitle=Yii::t("notification", "Attention!") ." ". Yii::t("admin", "You are using administration rights over {Username}", array(
				"{Username}"=>CHtml::encode($this->owner->username),
			));
		} else {
			$this->owner=$user;
		}
	}
}