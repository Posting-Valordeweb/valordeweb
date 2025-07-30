<?php
class ProfileController extends FrontController {

	public function actionSettings() {
		$changePassword=new ChangePasswordForm('manualChange');

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ChangePasswordForm'])) {
			$changePassword -> attributes = $_POST['ChangePasswordForm'];
			if($changePassword -> validate()) {
				$user = Yii::app()->user;
				$userModel = $user->loadModel();
				$userModel->salt = Hasher::generateSalt();
				$userModel->password = Hasher::hashPassword($changePassword->password, $userModel->salt);

				if($userModel -> save(false)) {
					$user->setFlash('success', Yii::t('user', 'Password has been changed'));
					$this->refresh();
				} else {
					$user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
				}
			}
		}

		$this->title=Yii::t("user", "Profile Settings");
		$changePasswordForm=$this->renderPartial("change_password", array(
			"form"=>$changePassword,
		), true);

		$this->render("settings", array(
			"changePasswordForm"=>$changePasswordForm,
		));
	}
}