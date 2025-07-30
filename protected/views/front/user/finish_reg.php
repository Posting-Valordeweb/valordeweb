<h1><?php echo Yii::t("user", "Go check your email and confirm RIGHT NOW") ?></h1>
<p>
<?php echo Yii::t("user", "A confirmation email has been sent on {ConfirmationEmail}", array(
    "{ConfirmationEmail}"=>"<strong>".$user->email ."</strong>",
));
?>
</p>
