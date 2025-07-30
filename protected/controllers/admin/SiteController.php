<?php
class SiteController extends BackController {
	public function actionIndex() {
		$this->title=Yii::t("admin", "Admin panel");
		$numOfScam=Yii::app()->db->createCommand()
			-> select("count(*)")
			-> from(Yii::app()->innerMail->scamTable)
			-> queryScalar();
		$numOfMissingTrans=Yii::app()->messages->totalMissingTranslations();
		$this->render("index", array(
			"numOfScam"=>$numOfScam,
			"numOfMissingTrans"=>$numOfMissingTrans,
		));
	}
}