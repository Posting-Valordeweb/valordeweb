<div class="btn-group mb-3">
	<a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage Translations") ?>
	</a>
</div>

<form method="post" class="mb-3">
    <?php echo CHtml::errorSummary($model, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'newName'); ?>

        <?php echo CHtml::activeTextField($model, 'newName', array(
            'class' => 'form-control',
        )); ?>

    </div>

    <button type="submit" class="btn btn-primary">
        <?php echo Yii::t("misc", "Update"); ?>
    </button>
</form>