<h3><?php echo Yii::t("mail", "Thank you for registration") ?></h3>
<p>
<?php echo Yii::t("mail", "Dear {Name}", array(
    "{Name}"=>CHtml::encode(Helper::mb_ucfirst($name)),
)) ?>,
<p>
<?php echo Yii::t("mail", "Thank you for registration at {BranUrl}. Click here: {VerifyUrl}", array(
    "{BranUrl}"=>Helper::getBrandUrl(),
    "{VerifyUrl}"=>CHtml::link($verifyUrl, $verifyUrl),
)); ?>
</p>

