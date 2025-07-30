<h1 class="mb-20"><?php echo CHtml::encode($this->title); ?></h1>
<p>
    <?php echo Yii::t("contact", "Contact page description") ?>
</p>

<form method="POST">
    <?php echo CHtml::errorSummary($form, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
    <?php echo CHtml::activeLabel($form, 'name'); ?>
    <?php echo CHtml::activeTextField($form, 'name', array('class' => 'form-control')); ?>
    </div>


    <div class="form-group">
    <?php echo CHtml::activeLabel($form, 'email'); ?>
    <?php echo CHtml::activeTextField($form, 'email', array('class' => 'form-control')); ?>
    </div>

    <div class="form-group">
    <?php echo CHtml::activeLabel($form, 'subject'); ?>
    <?php echo CHtml::activeTextField($form, 'subject', array('class' => 'form-control')); ?>
    </div>

    <div class="form-group">
    <?php echo CHtml::activeLabel($form, 'body'); ?>
    <?php echo CHtml::activeTextArea($form, 'body', array('class' => 'form-control', 'rows'=>6)); ?>
    </div>

    <?php if(Helper::isAllowedCaptcha()): ?>
    <div class="form-group<?php echo $form->hasErrors('verifyCode')  ? " is-invalid" : "" ?>">
        <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
            "siteKey"=>Yii::app()->params['recaptcha.public'],
            'model'=>$form,
            'attribute'=>'verifyCode',
            "wrapperOptions"=>array(
                'class'=>'recaptcha_wrapper'
            ),
        )); ?>
    </div>
    <?php endif; ?>

    <?php echo CHtml::submitButton(Yii::t("misc", "Submit"), array(
        'class' => 'btn btn-primary mb-20',
    )); ?>
</form>