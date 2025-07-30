<h1 class="mb-20"><?php echo Yii::t("user", "Register Form") ?></h1>

<p>
    <?php echo Yii::t("user", "{Click here} if you already have an account and just need to login.", array(
        "{Click here}"=>CHtml::link(Yii::t("misc", "Click here"), $this->createUrl("user/sign-in")),
    )) ?>
</p>

<form method="post" class="mb-20">
    <?php echo CHtml::errorSummary($user, null, null, array(
        'class' => 'alert alert-danger col-lg-offset-2',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'email'); ?>
        <?php echo CHtml::activeTextField($user, 'email', array(
            'class' => 'form-control login',
            'required'=>true,
            'type'=>'email',
            'placeholder'=>Yii::t("user", "Email"),
            'autofocus'=>true,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'password'); ?>
        <?php echo CHtml::activePasswordField($user, 'password', array(
            'class' => 'form-control password',
            'required'=>true,
            'placeholder'=>Yii::t("user", "Password"),
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'password2'); ?>
        <?php echo CHtml::activePasswordField($user, 'password2', array(
            'class' => 'form-control password',
            'required'=>true,
            'placeholder'=>Yii::t("user", "Re-Password"),
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'username'); ?>
        <?php echo CHtml::activeTextField($user, 'username', array(
            'class' => 'form-control login',
            'required'=>true,
            'placeholder'=>Yii::t("user", "Username"),
        )); ?>
    </div>

    <div class="form-group form-check">
        <?php echo CHtml::activeCheckBox($user, 'agree', array(
            'class' => 'form-check-input',
            'required'=>true,
        )); ?>
        <?php echo CHtml::activeLabel($user, 'agree', array(
            'class' => 'form-check-label'
        )); ?>
    </div>

    <?php if(Helper::isAllowedCaptcha()): ?>
    <div class="form-group">
        <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
            "siteKey"=>Yii::app()->params['recaptcha.public'],
            'model'=>$user,
            'attribute'=>'verifyCode',
            "widgetOpts"=>array(),
        )); ?>
    </div>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit"><?php echo Yii::t("user", "Sign up") ?></button>
</form>





