<?php
class CronController extends FrontController {
	public function init() {
		parent::init();
        $app_key = (string) Yii::app() -> params['app.command_key'];
		$get_key = (string) Yii::app() -> request -> getQuery('key');
		if(empty($app_key)) {
			throw new CHttpException(400, "Bad request");
		}
        if(strcmp($app_key, $get_key) !== 0) {
			throw new CHttpException(400, "Bad request");
		}
        @ini_set('max_execution_time', 0);
        @ini_set('max_input_time', -1);
    }

	public function actionSitemap() {
		$this->runCommand(array(
			'yiic', 'sitemap', 'index'
		));
	}

	public function actionGarbageCollector() {
		$this->runCommand(array(
			'yiic', 'garbagecollector', 'index'
		));
	}

	public function actionOnSaleChecker() {
		$this->runCommand(array(
			'yiic', 'checkonsale', 'index'
		));
	}

	protected function runCommand($args) {
		// Get command path
		$commandPath = Yii::app() -> getBasePath() . DIRECTORY_SEPARATOR . 'commands';

		// Create new console command runner
		$runner = new CConsoleCommandRunner();

		// Adding commands
		$runner -> addCommands($commandPath);

		// If something goes wrong return error
		$runner -> run ($args);
	}
}