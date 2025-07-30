<?php
class PostController extends AdminEditableController {
	public $website_id; // For event subscribers. They must know about what website ppl talking about

	public function actionIndex() {
		$box=Yii::app()->innerMail->box($this->owner);
		$pgNr=Yii::app()->request->getQuery('page', 1);
		$folder=$box->getValidFolderID(Yii::app()->request->getQuery('f', UserInnerMailBox::FOLDER_INBOX));
		$folderName=$box->getNameFolderByID($folder);

		$this->title=Yii::t("post", "folder_$folderName"). " - ". CHtml::encode($this->owner->username);

		$headers=$box->getHeaders($pgNr, $pgCnt, $total, $folder);
		$state=$folder==UserInnerMailBox::FOLDER_TRASH ? UserInnerMailBox::FOLDER_STATE_INVISIBLE : UserInnerMailBox::FOLDER_STATE_VISIBLE;

		$senders=array();
		foreach($headers as $header) {
			$senders[]=$header['companion_id'];
		}

		$criteria=new CDbCriteria;
		$criteria->index='id';
		$criteria->addInCondition('id', $senders);

		$summaryText=Helper::getSummaryText($pgNr, $total, Yii::app()->innerMail->headersPageSize);

		$pagination=new CPagination($total);
		$pagination->pageSize=Yii::app()->innerMail->headersPageSize;
		$pagination->currentPage=$pgNr;

		$dropDownItems=$this->renderPartial("dropdown_".$folderName, array(
		), true);

		$this->render("index", array(
			"headers"=>$headers,
			"folder"=>$folder,
			"total"=>$total,
			"pgCnt"=>$pgCnt,
			"pgNr"=>$pgNr,
			"box"=>$box,
			"dropDownItems"=>$dropDownItems,
			"pagination"=>$pagination,
			"senders"=>User::model()->findAll($criteria),
			"summaryText"=>$summaryText,
			"state"=>$state,
		));
	}

	public function actionChain($id) { // header_id
		$innerMail=Yii::app()->innerMail;
		$box = $innerMail->box($this->owner);
		if(!$header = $box->getHeader($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}

		$companion=User::model()->findByPk($header['companion_id']);

		$form=new PostForm;
		if(isset($_POST['PostForm']) AND is_array($_POST['PostForm'])) {
			$form->attributes=$_POST['PostForm'];
			$form->companion_id=$header['companion_id'];
			if($form->validate()) {
				$innerMail->attachEventHandler('onAddToChain', array('PostEvent', 'sendEmailOnMessageSent'));
				if($box->addToChain($header, $companion, $form->message)) {
					Yii::app()->user->setFlash('success', Yii::t("post", "Message has been sent"));
				} else {
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
				}
				$this->refresh();
			}
		}

		$pgNr=Yii::app()->request->getQuery('page', 1);
		$messages = $box->getMessages($header['id'], $pgNr, $pgCnt, $total);

		$messageTmpl=$this->renderPartial("messages", array(
			"pgNr"=>$pgNr,
			"pgCnt"=>$pgCnt,
			"messages"=>$messages,
			"box"=>$box,
			"companion"=>$companion,
			"user"=>$this->owner,
			"header"=>$header,
		), true);

		if(Yii::app()->request->isAjaxRequest) {
			exit($messageTmpl);
		}

		if(!$this->invisible) {
			$box->markAsRead($header['id']);
		}

		$block=$box->getBlock($header['companion_id']);
		$bind=BindWebsite::model()->with(array(
			"sale"=>array(
				"with"=>array(
					"website"=>array("select"=>"price, domain, idn"),
				),
			),
		))->findByPk($header['chain_id']);

		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(true).'/js/jquery.jscroll.min.js');
		Yii::app()->clientScript->registerCssFile(Yii::app()->request->getBaseUrl(true).'/css/chat.css');

		if($bind) {
			$sale=$bind->sale;
			$website=$bind->sale->website;
		} else {
			$sale=$website=null;
		}

		$this->title=Yii::t("post", "Chat with {Username}", array(
			"{Username}"=>CHtml::encode($companion->username),
		));

		$widget=$this->renderPartial("chat_widget", array(
			"form"=>$form,
			"companion"=>$companion,
			"user"=>$this->owner,
			"header"=>$header,
			"box"=>$box,
			"messages"=>$messageTmpl,
			"block"=>$block,
			"sale"=>$sale,
			"website"=>$website,
		), true);

		$this->render("add", array(
			"widget"=>$widget,
			"sale"=>$sale,
			"website"=>$website,
			"user"=>$this->owner,
		));
	}

	public function actionSend($id) { // website_id
		$onSale=Sale::model()->with(array(
			"website"=>array("select"=>"domain, price, idn"),
			"category"=>array("with"=>"translations"),
			//"user"=>array("scopes"=>array("active","confirmed"))
			"user",
		))->findByPk($id);
		if(!$onSale) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$this->website_id=$onSale->website->id;

		$scenario='new';
		$form=new PostForm($scenario);
		$box = Yii::app()->innerMail->box($this->owner);

		if(isset($_POST['PostForm']) AND is_array($_POST['PostForm'])) {
			$form->attributes=$_POST['PostForm'];
			$form->companion_id=$onSale->user->id;
			if($form->validate()) {
				$innerMail=Yii::app()->innerMail;
				$innerMail->attachEventHandler('onNewMessageCreated', array('PostEvent', 'sendEmailOnMessageSent'));
				$innerMail->attachEventHandler('onNewMessageCreated', array('PostEvent', 'bindWebsite'));
				if($box -> createNewMessage($onSale->user, $form->subject, $form->message)) {
					Yii::app()->user->setFlash('success', Yii::t("post", "Message has been sent"));
					$this->redirect(array("post/index"));
				} else {
					Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
					$this->refresh();
				}
			}
		}
		$this->title=Yii::t("post", "New message");
		$block=$box->getBlock($onSale->user->id);

		$widget = $this->renderPartial("new", array(
			"sale"=>$onSale,
			"website"=>$onSale->website,
			"user"=>$this->owner,
			"form"=>$form,
			"block"=>$block,
		), true);

		$this->render("add", array(
			"widget"=>$widget,
			"sale"=>$onSale,
			"website"=>$onSale->website,
		));
	}

	public function actionBlockedUsers() {
		$pgNr=Yii::app()->request->getQuery('page', 1);
		$box=Yii::app()->innerMail->box($this->owner);
		$blocked = $box->getBlockedUsers($pgNr, $pgCnt, $total);
		$criteria=new CDbCriteria;
		$criteria->select="username, id, status";
		$criteria->index="id";
		$criteria->addInCondition('id', array_keys($blocked));
		$users=User::model()->findAll($criteria);
		$summaryText=Helper::getSummaryText($pgNr, $total, Yii::app()->innerMail->blockedUsersPageSize);
		$this->title=Yii::t("post", "Blocked users");

		$this->render("blocked_users", array(
			"users"=>$users,
			"blocked"=>$blocked,
			"pgNr"=>$pgNr,
			"pgCnt"=>$pgCnt,
			"summaryText"=>$summaryText,
		));
	}

	public function actionBlockSender($id) { // User id (User which will be blocked by current user)
		if(!$blockedUser=User::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$box = Yii::app()->innerMail->box($this->owner);
		if($box->blockSender($blockedUser)) {
			Yii::app()->user->setFlash('success', Yii::t("post", "User has been blocked"));
		} else {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirectBack();
	}

	public function actionUnblockSender($id) { // Blocked user id
		if(!$blockedUser=User::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$box = Yii::app()->innerMail->box($this->owner);
		if($box->unBlockSender($blockedUser)) {
			Yii::app()->user->setFlash('success', Yii::t("post", "User has been unblocked"));
		} else {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirectBack();
	}

	public function actionReportScam($id) {
		$blockedUser=User::model()->findByPk($id);
		if(!$blockedUser=User::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$box = Yii::app()->innerMail->box($this->owner);
		$chain_id=Yii::app()->request->getQuery('chain_id');
		if(!$header=$box->getHeaderByChainID($chain_id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		if($box->reportScam($blockedUser, $chain_id)) {
			Yii::app()->user->setFlash('success', Yii::t("post", "User has been blocked and marked as a spammer"));
		} else {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirectBack();
	}

	public function actionRestoreFromTrash() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			if($box->restoreFromTrash($_POST['header'])) {
				Yii::app()->user->setFlash('success', Yii::t("post", "Messages have been restored"));
			} else {
				Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			}
		}
		$this->redirectBack();
	}

	public function actionCompletelyRemove() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			if($box->completelyRemove($_POST['header'])) {
				Yii::app()->user->setFlash('success', Yii::t("post", "Messages have been completely removed"));
			} else {
				Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			}
		}
		$this->redirectBack();
	}

	public function actionMoveToTrash() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->moveToTrashFolder($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages have been moved to trash folder"));
		}
		$this->redirectBack();
	}

	public function actionMarkAsRead() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->markAsRead($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages have been marked as read"));
		}
		$this->redirectBack();
	}

	public function actionMarkAsStar() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->markAsStarred($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages have been moved to Starred folder"));
		}
		$this->redirectBack();
	}

	public function actionRemoveFromStar() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->removeFromStarred($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages have been removed from Starred folder"));
		}
		$this->redirectBack();
	}

	public function actionRemoveFromImportant() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->removeFromImportant($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages have been removed from Important folder"));
		}
		$this->redirectBack();
	}

	public function actionMarkAsImportant() {
		$box = Yii::app()->innerMail->box($this->owner);
		if(isset($_POST['header']) AND is_array($_POST['header'])) {
			$rows=$box->markAsImportant($_POST['header']);
			Yii::app()->user->setFlash('success', Yii::t("post", "Selected messages has been marked as Important"));
		}
		$this->redirectBack();
	}

	private function redirectBack() {
		$referrer=Yii::app()->request->urlReferrer;
		$url=$referrer ? $referrer : $this->createUrl("post/index");
		$this->redirect($url);
	}
}