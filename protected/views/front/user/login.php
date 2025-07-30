<div class="row mb-20">
    <div class="col-md-6">
        <h3><?php echo Yii::t("user", "Login Form") ?></h3>
        <hr>
        <form method="post">
            <?php echo CHtml::errorSummary($login_form, null, null, array(
                'class' => 'alert alert-danger col-lg-offset-2',
            )); ?>

            <div class="form-group">
                <?php echo CHtml::activeLabel($login_form, 'email'); ?>
                <?php echo CHtml::activeTextField($login_form, 'email', array('class' => 'form-control login')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::activeLabel($login_form, 'password'); ?>
                <?php echo CHtml::activePasswordField($login_form, 'password', array('class' => 'form-control password')); ?>
            </div>

            <?php if(Helper::isAllowedCaptcha()): ?>
            <div class="form-group ">
                <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
                    "siteKey"=>Yii::app()->params['recaptcha.public'],
                    'model'=>$login_form,
                    'attribute'=>'verifyCode',
                    "widgetOpts"=>array(),
                )); ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <div class="form-check">
                    <?php echo CHtml::activeCheckBox($login_form, 'remember', array(
                        'class'=>'form-check-input'
                    )); ?>
                    <label class="form-check-label" for="LoginForm_remember">
                        <?php echo Yii::t("user", "Remember me"); ?>
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Sign in</button>

        </form>

        <ul class="list-group mt-20">
            <li class="list-group-item">
                <i class="fas fa-lock"></i>
                &nbsp;
                <a href="<?php echo $this->createUrl("user/forgot-password") ?>">
                    <?php echo Yii::t("user", "Forgot your password?") ?>
                </a>
            </li>
            <li class="list-group-item">
                <i class="fas fa-user-plus"></i>
                &nbsp;
                <?php echo Yii::t("user", "Don't have an account yet? {Sign up now}!", array(
                    "{Sign up now}"=>CHtml::link(Yii::t("user", "Sign up now"), $this->createUrl("user/sign-up")),
                )) ?>
            </li>
        </ul>

    </div>
    <div class="col-md-6">
        <h3><?php echo Yii::t("user", "Need an account?") ?></h3>
        <hr>
        <p>
        <?php echo Yii::t("user", "Creating a {InstalledUrl} account is fast, easy, and free", array(
            "{InstalledUrl}"=>"<strong>".Helper::getBrandUrl()."</strong>",
        )) ?>
        </p>
        <a href="<?php echo $this->createUrl("user/sign-up") ?>" class="btn btn-primary"><?php echo Yii::t("user", "Create an account") ?></a>
    </div>
</div>
