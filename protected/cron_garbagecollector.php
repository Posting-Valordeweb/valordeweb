<?php
mb_internal_encoding('UTF-8');
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

$config=dirname(__FILE__).'/config/console.php';

// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

defined('YII_DEBUG') or define('YII_DEBUG',false);

require_once(dirname(__FILE__).'/../framework/yii.php');

$app=Yii::createConsoleApplication($config);
$app->setTimeZone($app->params['app.timezone']);
$app->commandRunner->addCommands(YII_PATH.'/cli/commands');

$_SERVER['argv'] = array(
	Yii::app()->request->scriptFile,
	'garbagecollector',
	'index',
);

$env=@getenv('YII_CONSOLE_COMMANDS');

if(!empty($env))
	$app->commandRunner->addCommands($env);

$app->run();