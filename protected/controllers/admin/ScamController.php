<?php
class ScamController extends BackController {
	public function actionIndex() {
		$this->title=Yii::t("scam", "Scam reports");
		$numOfScam=Yii::app()->db->createCommand()
			-> select("count(*)")
			-> from(Yii::app()->innerMail->scamTable)
			-> queryScalar();
		$sql="SELECT * FROM ".Yii::app()->innerMail->scamTable;
		$dataProvider=new CSqlDataProvider($sql, array(
			"totalItemCount"=>$numOfScam,
			"pagination"=>array(
				'pageSize'=>10,
			),
		));

		$userStack=array();
		$data=$dataProvider->getData();

		foreach($data as $row) {
			$userStack[]=$row['sender_id'];
			$userStack[]=$row['scammer_id'];
		}
		$userStack=array_unique($userStack);
		$criteria=new CDbCriteria;
		$criteria->index="id";
		$criteria->select="id, username";
		$criteria->addInCondition('id', $userStack);
		$users=User::model()->findAll($criteria);

		$this->render("index", array(
			"dataProvider"=>$dataProvider,
			"data"=>$data,
			"users"=>$users,
		));
	}

	public function actionDialog() { // chain id
		$sender_id=Yii::app()->request->getQuery('sender_id');
		$chain_id=Yii::app()->request->getQuery('chain_id');
		$pgNr=Yii::app()->request->getQuery('page', 1);

		if(!$sender=User::model()->findByPk($sender_id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$box=Yii::app()->innerMail->box($sender);
		if(!$header=$box->getHeaderByChainID($chain_id)) {
			throw new CHttpException(404, Yii::t("scam", "The dialog has been completely removed"));
		}
		$scammer=User::model()->findByPk($header['companion_id']);

		$this->title=Yii::t("scam", "The dialog between {Sender} and {Scammer}", array(
			"{Sender}"=>CHtml::encode($sender->username),
			"{Scammer}"=>CHtml::encode($scammer->username)
		));

		Yii::app()->innerMail->messagesPageSize=50;
		$messages = $box->getMessages($header['id'], $pgNr, $pgCnt, $total, "ASC");

		$pages=new CPagination($total);
		$pages->pageSize=Yii::app()->innerMail->messagesPageSize;
		$pages->currentPage=$pgNr-1;

		$referrer=Yii::app()->request->urlReferrer;
		$backUrl=$referrer ? $referrer : $this->createUrl("admin/scam/index");

		$this->render("dialog", array(
			"messages"=>$messages,
			"sender"=>$sender,
			"scammer"=>$scammer,
			"box"=>$box,
			"pages"=>$pages,
			"backUrl"=>$backUrl,
		));
	}

	public function actionRestrictUser($id) { // user id
		$transaction=Yii::app()->db->beginTransaction();
		try {
			User::model()->updateByPk($id, array(
				"can_send_message"=>User::DISALLOW_MESSAGE,
			));
			Yii::app()->db->createCommand()->delete(Yii::app()->innerMail->scamTable, "scammer_id=:scammer_id", array(
				":scammer_id"=>$id,
			));
			$transaction->commit();
			Yii::app()->user->setFlash('success', Yii::t('scam', 'User has been restricted'));
		} catch(Exception $e) {
			$transaction->rollback();
			Yii::log($e->getMessage(), 'error', 'application.admin.scam.restrictuser');
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		}
		$this->redirect(array("admin/scam/index"));
	}

	public function actionRemove() {
		$scammer_id=Yii::app()->request->getQuery('scammer_id');
		$sender_id=Yii::app()->request->getQuery('sender_id');
		Yii::app()->db->createCommand()->delete(Yii::app()->innerMail->scamTable, 'scammer_id=:scammer_id AND sender_id=:sender_id', array(
			":scammer_id"=>$scammer_id,
			":sender_id"=>$sender_id,
		));
		Yii::app()->user->setFlash('success', Yii::t("notification", "Record has been deleted"));
		$this->redirectBack();
	}

	public function actionFlush() {
		Yii::app()->db->createCommand()->truncateTable(Yii::app()->innerMail->scamTable);
		Yii::app()->user->setFlash('success', Yii::t("notification", "All records have been removed"));
		$this->redirect(array("admin/scam/index"));
	}

	private function redirectBack() {
		$referrer=Yii::app()->request->urlReferrer;
		$url=$referrer ? $referrer : $this->createUrl("admin/scam/index");
		$this->redirect($url);
	}
}