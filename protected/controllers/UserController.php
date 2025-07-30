<?php
class UserController extends FrontController {
	public function events() {
		return array(
			'onUserCreated' => array(
				array('UserEvent', 'sendTokenOnEmail'),
				array('UserEvent', 'createUserServer'),
			),
		);
	}

	public function actionSignIn() {
		$user = Yii::app() -> user;
		if(!$user->isGuest) {
			Yii::app()->user->logout();
		}
		$this->title=Yii::t("user", "Sign in") ." | ". Helper::getInstalledUrl();

		$loginForm = new LoginForm();

		if(Yii::app() -> request -> isPostRequest AND !empty($_POST['LoginForm'])) {
			$loginForm -> attributes = $_POST['LoginForm'];
			if($loginForm -> validate()) {
				$this -> redirect(Yii::app() -> user -> returnUrl);
			}
		}
		$this->render("login", array(
			'login_form' => $loginForm,
		));
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(array("site/index"));
	}

	public function actionSignUp() {
		if(!Yii::app()->user->isGuest) {
			Yii::app()->user->logout();
		}
		$user = new User();
		$this -> title = Yii::t("user", "Register new account"). " | " . Helper::getInstalledUrl();
		$flag = false;

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['User'])) {
			$user -> attributes = $_POST['User'];
			if($user -> validate()) {
				$user->salt = Hasher::generateSalt();
				$user->password = Hasher::hashPassword($user->password, $user->salt);
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					$user -> role = User::ROLE_USER;
					$user -> email_confirmed = User::EMAIL_NOTCONFIRMED;
					$user -> can_send_message = User::ALLOW_MESSAGE;
					$user -> status = User::STATUS_ACTIVE;
                    $user -> lang_id = Yii::app()->language;
					if(!$user -> save(false)) {
						throw new CException("Unable to save user");
					}
					$this->onUserCreated(new CEvent($this, array(
						"user"=>$user,
					)));
					$transaction -> commit();
					$flag = true;
				} catch (Exception $e) {
					$transaction -> rollback();
					Yii::log($e->getMessage(), 'error', 'application.user.register');
					Yii::app() -> user -> setFlash('warning', Yii::t("notification", 'Server temporarily unavailable. Please, try to register a little bit later'));
					$this->refresh();
				}
			}
		}

		$tmpl = $flag ? "finish_reg" : "registration";

		$this->render($tmpl, array(
			"user" => $user
		));
	}

	public function actionConfirm() {
		$tok = Yii::app()->getRequest()->getQuery('token');
		$type = Yii::app()->getRequest()->getQuery('t');
        $this->title=Yii::t("user", "Email confirmation"). " | ".Helper::getInstalledUrl();
		if(!$token = UserToken::model()->get($tok, $type)) {
			throw new CHttpException(400, Yii::t("notification", "The token is expired or does not valid. Please, try to register new account"));
		}
		$transaction = Yii::app()->db->beginTransaction();
		$error = false;
		try {
			$user = new User;
			$token->status = UserToken::STATUS_ACTIVE;
			if(!$user->updateByPk($token->user_id, array("email_confirmed"=>User::EMAIL_CONFIRMED)) OR !$token->save()) {
				throw new CException("Unable to save records");
			}
			$transaction->commit();
		} catch (Exception $e) {
			$error = true;
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.user.confirm');
		}
		$this->render("confirm", array(
			'error' => $error,
		));
	}

	public function actionForgotPassword() {
		$form = new ForgotPasswordForm();
		$this->title=Yii::t("user", "Forgot your password?"). " | ".  Helper::getInstalledUrl();

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ForgotPasswordForm'])) {
			$form -> attributes = $_POST['ForgotPasswordForm'];
			if($form -> validate()) {
				$userModel = User::model()->findByAttributes(array(
					'email' => $form->email,
				));

				$userToken = new UserToken;
				if(!$token = $userToken->createResetPassword($userModel)) {
					$form -> addError(null, Yii::t("notification", "Server temporarily unavailable. Try again later."));
				} else {
                    $mail = new YiiMailer();

                    $subject = Yii::t("user", "Password recovery").'. '. Helper::getBrandUrl();
                    $mail->setSubject($subject);
                    $mail->setFrom(Yii::app()->params['notification.email'], Yii::app()->params['notification.name']);
                    $mail->clearReplyTos();
                    $mail->setTo($userModel->email);
                    $mail->setView('user/reset_password');
                    $mail->setData(array(
                        "mailer"=>$mail,
                        "name"=>Helper::mb_ucfirst($userModel->username),
                        "user"=>$userModel,
                        "recoveryUrl"=>$this->createAbsoluteUrl("user/reset-password", array("t"=>$token->type, "token" => $token->token)),
                    ));
                    $mail->setAltText(Yii::t('notification', 'Please, use mail client which support HTML markup'));

					if($mail->send()) {
						Yii::app()->user->setFlash('success', Yii::t("notification", "We've sent you email with instructions. Please check your inbox"));
					} else {
						Yii::app()->user->setFlash('danger', Yii::t("notification", "An error occurred while sending email"));
					}

					$this->refresh();
				}
			}
		}

		$this->render("forgot_password", array(
			'form' => $form,
		));
	}

	public function actionResetPassword() {
		$tok = Yii::app()->getRequest()->getQuery('token');
		$type = Yii::app()->getRequest()->getQuery('t');
		if(!$token = UserToken::model()->get($tok, $type)) {
            throw new CHttpException(400, Yii::t("notification", "The token is expired or does not valid. Please, try to register new account"));
		}
		$this->title = Yii::t("user", 'Set new password');

		$scenario = 'tokenChange';
		$form = new ChangePasswordForm($scenario);

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ChangePasswordForm'])) {
			$form -> attributes = $_POST['ChangePasswordForm'];
			if($form -> validate()) {
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					$token->user->salt = Hasher::generateSalt();
					$token->user->password = Hasher::hashPassword($form->password, $token->user->salt);

					if(!$token->user->save(false)) {
						throw new CException("Unable to save new password. Restore password form");
					}

					$token->status = UserToken::STATUS_ACTIVE;
					if(!$token->save()) {
						throw new CException("Unable to update token. Restore password form");
					}

					$transaction -> commit();
					Yii::app() -> user -> setFlash('user', 'The password has been changed. Use new password to login');
					$this->redirect(array("user/sign-in"));
				} catch (Exception $e) {
					$transaction -> rollback();
					Yii::log($e->getMessage(), 'error', 'application.user.reset_password');
					Yii::app() -> user -> setFlash('error', Yii::t('notification', 'Server temporarily unavailable. Try again later.'));
					$this->refresh();
				}
			}
		}

		$this -> render('change_password', array(
			"form" => $form,
			"token" => $token,
		));
	}

	public function onUserCreated(CEvent $event) {
		$this->raiseEvent('onUserCreated', $event);
	}
}