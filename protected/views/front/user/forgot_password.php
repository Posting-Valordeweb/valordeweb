<h1 class="mb-20"><?php echo Yii::t("user", "Forgot your password?") ?></h1>
<p>
    <?php echo Yii::t("user", "Forgot password instruction") ?>
</p>
<form method="post" class="mb-20">
    <?php echo CHtml::errorSummary($form, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($form, 'email'); ?>
        <?php echo CHtml::activeTextField($form, 'email', array('class' => 'form-control')); ?>
    </div>

    <?php if(Helper::isAllowedCaptcha()): ?>
    <div class="form-group">
        <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
            "siteKey"=>Yii::app()->params['recaptcha.public'],
            'model'=>$form,
            'attribute'=>'verifyCode',
            "widgetOpts"=>array(),
        )); ?>
    </div>
    <?php endif; ?>

    <?php echo CHtml::submitButton(Yii::t("misc", "Submit"), array(
        'class' => 'btn btn-large btn-primary',
    )); ?>
    &nbsp; <a href="<?php echo $this->createUrl("user/sign-in") ?>"><?php echo Yii::t("user", "or wait, I remember!") ?></a>
</form>