<form method="POST">
    <?php echo CHtml::errorSummary($form, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>
    <?php if($scenario): ?>
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

    <?php echo CHtml::submitButton(Yii::t("misc", "Submit"), array(
        'class' => 'btn btn-large btn-primary',
    )); ?>
</form>