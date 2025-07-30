<html>
<head>
    <title><?php echo CHtml::encode($data['mailer']->Subject) ?></title>
</head>
<body>
<?php echo $content ?>
<?php echo Yii::t("mail", "Regards", array(), null, $data['user']->lang_id) ?>,<br/>
<?php echo Yii::t("mail", "{Brandname} administration", array(
    "{Brandname}"=> Helper::getBrandUrl(),
), null, $data['user']->lang_id)
?>
<br/>
<small><?php echo Yii::t("mail", "Please, do not reply to this message") ?></small>
</body>
</html>