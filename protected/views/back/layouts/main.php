<!DOCTYPE html>
<html lang="<?php echo Yii::app() -> language ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="author" content="php8developer.com">
<meta name="dcterms.rightsHolder" content="php8developer.com">
<link rel="shortcut icon" href="<?php echo Yii::app()->getBaseUrl(true) ?>/favicon.ico" />
<link href="<?php echo Yii::app()->baseUrl ?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->baseUrl ?>/css/fontawesome.min.css" rel="stylesheet">

<link rel="apple-touch-icon" href="<?php echo Yii::app()->getBaseUrl(true) ?>/images/touch-114.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo Yii::app()->getBaseUrl(true) ?>/images/touch-72.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo Yii::app()->getBaseUrl(true)  ?>/images/touch-114.png">

<?php Yii::app()->clientScript->registerCoreScript('jquery') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/bootstrap.bundle.min.js') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/admin.js?v=0.1') ?>

<title><?php echo CHtml::encode($this->title) ?></title>
</head>

<body>
<?php echo $content ?>
</body>
</html>