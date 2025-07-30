<?php if(!$error): ?>
<h1>
    <?php echo Yii::t("user", "Congratulations!") ?>
</h1>
<p>
<?php echo Yii::t("user", "You have successfully confirmed your email address.") ?>
<br/><br/>
<a href="<?php echo $this->createUrl("user/sign-in") ?>" class="btn btn-primary""><?php echo Yii::t("user", "Sign in") ?></a>
</p>
<?php else: ?>
<div class="alert alert-warning">
    <strong><?php echo Yii::t("notification", "Attention!") ?></strong> <?php echo Yii::t("notification", "An error has occurred while trying to confirm your email. Please try again later.") ?>
</div>
<?php endif; ?>