<?php
class WebsiteController extends BackController {
	public function actionIndex() {
		$scenario = "search";
		$this->title=Yii::t("website", "Manage Websites");
		$website=new Website($scenario);
		$website->unsetAttributes();
		if(isset($_GET['Website'])) {
			$website->attributes = $_GET['Website'];
		}
		$this->render("index", array(
			"website"=>$website,
		));
	}

	public function actionCalculate($id) { // website ID int
		if(!$website=Website::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$args = array('yiic', 'calculate', 'update',
			"--domain={$website->domain}",
			"--idn={$website->idn}",
			"--ip=".gethostbyname($website->domain),
			"--wid={$website->id}"
		);
		$commandPath = Yii::app() -> getBasePath() . DIRECTORY_SEPARATOR . 'commands';
		$runner = new CConsoleCommandRunner();
		$runner -> addCommands($commandPath);
		$referrer=Yii::app()->request->getUrlReferrer();
		$backUrl=$referrer ? $referrer : $this->createUrl("website/index");
		if($error = $runner -> run ($args)) {
			Yii::app()->user->setFlash('danger', Yii::t("error_code", "Calculation Error Code $error"));
		} else {
			Yii::app()->user->setFlash('success', Yii::t("website", "The website's estimated price has been recalculated. {Click here} to see a result", array(
				'{Click here}'=>CHtml::link(Yii::t("misc", "Click here"), CHtml::normalizeUrl(array("website/show", "domain"=>$website->domain)), array(
					"target"=>"_blank",
				)
			))));
		}
		$this->redirect($backUrl);
	}

	public function actionRemoveFromSale($id) { // website ID
		if(!$website=Website::model()->findByPk($id)) {
			throw new CHttpException(404, Yii::t("notification", "The page you are looking for doesn't exists"));
		}
		$transaction=Yii::app()->db->beginTransaction();
		try {
			SaleEvent::removeFromSale($id);
			$transaction->commit();
			Yii::app()->user->setFlash('success', Yii::t('website', 'Website {Website} has been removed from sale', array(
				"{Website}"=>"<strong>".$website->idn."</strong>",
			)));
		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
			Yii::log($e->getMessage(), 'error', 'application.admin.website.removefromsale');
		}
		$this->redirect(array("admin/website/index"));
	}

	public function actionDelete($id) { // website ID int
		$transaction=Yii::app()->db->beginTransaction();
		try {
			Website::model()->deleteByPk($id);
			WebdataAlexa::model()->deleteByPk($id);
			WebdataAntivirus::model()->deleteByPk($id);
			WebdataCatalog::model()->deleteByPk($id);
			WebdataLocation::model()->deleteByPk($id);
			WebdataMetaTags::model()->deleteByPk($id);
			WebdataSearchEngine::model()->deleteByPk($id);
			WebdataSocial::model()->deleteByPk($id);
			WebdataWhois::model()->deleteByPk($id);

			SaleEvent::removeFromSale($id);

			$transaction->commit();

			$this->renderPartial("//{$this->_end}/site/flash", array(
				"messages"=>array(
					'success'=>Yii::t("website", "Website has been deleted"),
				),
			));

		} catch(Exception $e) {
			$transaction -> rollback();
			Yii::log($e->getMessage(), 'error', 'application.admin.website.delete');
			$this->renderPartial("//{$this->_end}/site/flash", array(
				"messages"=>array(
					'danger'=>Yii::t("notification", "An internal error occurred. Please try again later"),
				),
			));
		}

	}
}