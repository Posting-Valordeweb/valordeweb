<h1 class="mb-20"><?php echo CHtml::encode($this -> title) ?></h1>
<form method="post" class="mb-20">
    <?php echo CHtml::errorSummary($form, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <?php if($form->scenario == "manualChange"): ?>
        <div class="form-group">
            <?php echo CHtml::activeLabel($form, 'oldpassword'); ?>
            <?php echo CHtml::activePasswordField($form, 'oldpassword', array('class' => 'form-control')); ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($form, 'password'); ?>
        <?php echo CHtml::activePasswordField($form, 'password', array('class' => 'form-control')); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($form, 'password2'); ?>
        <?php echo CHtml::activePasswordField($form, 'password2', array('class' => 'form-control')); ?>
    </div>

    <?php echo CHtml::submitButton(Yii::t("user", "Reset password"), array(
        'class' => 'btn btn-large btn-primary',
    )); ?>
</form>