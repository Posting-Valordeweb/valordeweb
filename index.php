<?php
mb_internal_encoding('UTF-8');
error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/web.php';

// defined('YII_DEBUG') or define('YII_DEBUG', false);
// defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
$app=Yii::createWebApplication($config);
$app->setTimeZone($app->params['app.timezone']);
$app->run();