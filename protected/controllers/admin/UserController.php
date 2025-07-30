<?php
class UserController extends BackController {
	public function events() {
		return array(
			'onUserCreated' => array(
				array('UserEvent', 'sendTokenOnEmail'),
				array('UserEvent', 'createUserServer'),
			),
			'onUserDeleted'=>array(
				array('UserEvent', 'garbageCollector'),
			),
			'onUserUpdated'=>array(
			),
		);
	}

	public function actionIndex() {
		$scenario = "search";
		$this->title=Yii::t("user", "Manage Users");
		$user = new User($scenario);
		$user -> unsetAttributes();
		if(isset($_GET['User'])) {
			$user -> attributes = $_GET['User'];
		}
		$this->render("index", array(
			'user' => $user,
		));
	}

	public function actionCreate() {
		$scenario = 'adminCreate';

		$user = new User($scenario);
		$user->email_confirmed = User::EMAIL_CONFIRMED;
		$user->lang_id=Yii::app()->language;

		$this -> title = Yii::t("user", "Create User");
		if(Yii::app() -> request -> isPostRequest && !empty($_POST['User'])) {
			$user -> attributes = $_POST['User'];
			if($user -> validate()) {
				$user->salt = Hasher::generateSalt();
				$user->password = Hasher::hashPassword($user->password, $user->salt);
				$transaction = Yii::app() -> db -> beginTransaction();
				try {
					if(!$user -> save(false)) {
						throw new CException("Unable to save user");
					}
					if($user->hasConfirmedEmail()) {
						$this->detachEventHandler('onUserCreated', array('UserEvent', 'sendTokenOnEmail'));
					}

					$this->onUserCreated(new CEvent($this, array(
						"user"=>$user,
					)));

					$transaction -> commit();
					Yii::app() -> user -> setFlash('success', Yii::t('user', 'User has been created'));
					$this->redirect(array("admin/user/index"));
				} catch (Exception $e) {
					$transaction -> rollback();
					Yii::log($e->getMessage(), 'danger', 'application.admin.user.create');
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					$this->refresh();
				}
			}
		}
		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any language"), '-'=>'----------'),
			CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage'))
		);
        $roleList=User::getRoleList();
        unset($roleList[User::ROLE_ROOT]);

		$this->render("create", array(
			"user" => $user,
			"scenario"=>$scenario,
			"languages"=>$languages,
            "roleList"=>$roleList,
		));
	}

	public function actionView($id) {
		$user = $this->loadModel($id);
		$this->title=$user->email. " : ". Yii::t("user", "User info");
		$onSale = Sale::model()->countByAttributes(array(
			"user_id"=>$id
		));
		$this->render("view", array(
			"user" => $user,
			"onSale"=>$onSale,
		));
	}

	public function actionUpdate($id) {
		$user = $this->loadModel($id);
		$oldUser = clone $user;
		$scenario = "adminUpdate";
		$user -> scenario = $scenario;
		$this -> title = $user->email. " : ". Yii::t("user", "Edit user data");

		if(Yii::app() -> request -> isPostRequest && !empty($_POST['User'])) {
			$user -> attributes = $_POST['User'];
			if($user -> save()) {
				$this->onUserUpdated(new CEvent($this, array(
					"user"=>$user,
					"oldUser"=>$oldUser,
				)));
				Yii::app() -> user -> setFlash('success', Yii::t('notification', 'Record has been successfully modified'));
				$this->redirect(array("admin/user/view", "id"=>$id));
			}
		}

		$languages = CMap::mergeArray(
			array(''=>Yii::t("language", "Choose any language"), '-'=>'----------'),
			CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage'))
		);
        $roleList=User::getRoleList();
        unset($roleList[User::ROLE_ROOT]);

		$this->render("update", array(
			"user" => $user,
			"scenario"=>$scenario,
			"languages"=>$languages,
            "roleList"=>$roleList,
		));
	}

	public function actionDelete($id) {
		$user = $this->loadModel($id);
		$transaction=Yii::app()->db->beginTransaction();
		try {
			$user->status = User::STATUS_DELETED;
			$user->save(false);
			$this->onUserDeleted(new CEvent($this, array(
				"user"=>$user,
			)));
			$transaction->commit();
			$this->renderPartial("//{$this->_end}/site/flash", array(
				"messages"=>array(
					'success'=>Yii::t("user", "User has been deleted"),
				),
			));
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.admin.user.delete');
			$this->renderPartial("//{$this->_end}/site/flash", array(
				"messages"=>array(
					'danger'=>Yii::t("notification", "An internal error occurred. Please try again later"),
				),
			));
		}
	}

	public function actionResetPassword($id) {
		$user = $this->loadModel($id);
		$this -> title = $user->email. " : ". Yii::t("user", "Reset password");

		$scenario=null;
		$form = new ChangePasswordForm($scenario);
		if(Yii::app() -> request -> isPostRequest && !empty($_POST['ChangePasswordForm'])) {
			$form -> attributes = $_POST['ChangePasswordForm'];
			if($form -> validate()) {
				$user->salt = Hasher::generateSalt();
				$user->password = Hasher::hashPassword($form->password, $user->salt);
				if($user->save(false)) {
					Yii::app() -> user -> setFlash('success', Yii::t("user", "Password has been changed"));
				} else {
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
				}
				$this -> redirect(array("admin/user/view", "id" => $user->id));
			}
		}

		$this->render("change_password", array(
			"form" => $form,
			"scenario"=>$scenario,
		));
	}

	public function actionLogin() {
		$user = Yii::app() -> user;
		if(!$user->isGuest) {
			Yii::app()->user->logout();
		}
        $this->title=Yii::t("user", "Sign in"). " | ". Yii::t("admin", "Admin panel");
		$this->layout="/{$this->_end}/layouts/login";

		$loginForm = new LoginForm();

		if(Yii::app() -> request -> isPostRequest AND !empty($_POST['LoginForm'])) {
			$loginForm -> attributes = $_POST['LoginForm'];
			if($loginForm -> validate()) {
				$this -> redirect(array("admin/site/index"));
			}
		}

		$this->render("login", array(
			'login_form' => $loginForm,
		));
	}

	public function actionLogout() {
		Yii::app()->user->logout();
		Yii::app()->session->open();
		Yii::app()->user->setFlash('warning', Yii::t("user", "You have been disconnected from the session"));
		Yii::app()->user->setReturnUrl(Yii::app()->request->urlReferrer);
		$this->redirect(Yii::app()->user->loginUrl);
	}

	protected function loadModel($id) {
		if($this->user->id==$id) {
			$model=$this->user;
		}
		if(!isset($model) AND !($model = User::model() -> findByPk($id))) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		if($model->isSuperUser() AND !$this->user->isSuperUser()) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		return $model;
	}

	public function onUserCreated(CEvent $event) {
		$this->raiseEvent('onUserCreated', $event);
	}

	public function onUserDeleted(CEvent $event) {
		$this->raiseEvent('onUserDeleted', $event);
	}

	public function onUserUpdated(CEvent $event) {
		$this->raiseEvent('onUserUpdated', $event);
	}

}
