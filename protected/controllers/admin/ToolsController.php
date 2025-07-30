<?php
class ToolsController extends BackController {
    public function init()
    {
        parent::init();
        @ini_set('max_execution_time', 0);
        @ini_set('max_input_time', -1);
    }

    public function actionSitemap() {
		$args = array('yiic', 'sitemap');
		if($error = $this->runCommand($args)) {
			Yii::app()->user->setFlash("danger", Yii::t("error_code", "Sitemap error code $error"));
		} else {
			Yii::app()->user->setFlash("success", Yii::t("tools", "Sitemap has been generated"));
		}
		$this->redirectBack();
	}

	public function actionClearCache() {
		Yii::app()->cache->flush();
		Yii::app()->user->setFlash("success", Yii::t("tools", "Cache has been cleared"));
		$this->redirectBack();
	}

	public function actionCheckOnSale() {
		$args=array('yiic', 'checkonsale');
		if($error=$this->runCommand($args)) {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		} else {
			Yii::app()->user->setFlash("success", Yii::t("tools", "All websites that are on sale have been checked"));
		}
		$this->redirectBack();
	}

	public function actionGarbageCollector() {
		$args=array('yiic', 'garbagecollector');
		if($error=$this->runCommand($args)) {
			Yii::app()->user->setFlash('danger', Yii::t("notification", "An internal error occurred. Please try again later"));
		} else {
			Yii::app()->user->setFlash("success", Yii::t("tools", "Expired data has been cleared"));
		}
		$this->redirectBack();
	}

	public function actionBulkImport()
    {
        $args = array('yiic', 'bulkimport', 'run');
        ob_start();
        $this->runCommand($args);
        $content = ob_get_clean();
        Yii::app()->user->setFlash('warning', nl2br($content));
        $this->redirectBack();
    }

	private function redirectBack() {
		$referrer=Yii::app()->request->urlReferrer;
		$url=$referrer ? $referrer : $this->createUrl("admin/site/index");
		$this->redirect($url);
	}

	private function runCommand($args)
    {
        $commandPath = Yii::app() -> getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        return $runner->run($args);
    }
}