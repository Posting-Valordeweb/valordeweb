<h3><?php echo Yii::t("mail", "Forgot password?") ?></h3>
<p>
<?php echo Yii::t("mail", "Dear {Name}", array(
    "{Name}"=>CHtml::encode(Helper::mb_ucfirst($name)),
));
?>,
</p>
<p>
<?php echo Yii::t("mail", "We've received a request from you to reset a password. If you didn't do it, then just ignore this message.") ?>
<br/>
<?php echo Yii::t("mail", "To reset password open following link: {RecoveryLink} and enter new password.", array(
    "{RecoveryLink}"=>CHtml::link($recoveryUrl, $recoveryUrl).'<br>',
));
?>
</p>