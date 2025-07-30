<div class="btn-group mb-3">
	<a href="<?php echo $this->createUrl("admin/language/index") ?>" class="btn btn-primary">
		<i class="fa fa-list"></i> <?php echo Yii::t('language', 'Manage existing languages') ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-success">
		<i class="fa fa-language"></i> <?php echo Yii::t("language", "Manage Translations") ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/import") ?>" class="btn btn-warning">
		<i class="fa fa-upload"></i> <?php echo Yii::t("language", "Import Translation") ?>
	</a>
</div>

<h5 class="mb-3"><?php echo Yii::t("language", "Please choose language you want to export") ?></h5>
<form method="post">
    <?php echo CHtml::errorSummary($model, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'language'); ?>
        <?php echo CHtml::activeDropDownList($model, 'language', $languages, array(
            'class' => 'form-control',
            'options' => array(
                '-' => array(
                    'disabled' => 'disabled',
                ),
                '' => array(
                    'readonly' => 'readonly',
                ),
            ),
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'trans_language'); ?>
        <?php echo CHtml::activeDropDownList($model, 'trans_language', $languages, array(
            'class' => 'form-control',
            'options' => array(
                '-' => array(
                    'disabled' => 'disabled',
                ),
                '' => array(
                    'readonly' => 'readonly',
                ),
            ),
        )); ?>
    </div>

    <button type="submit" class="btn btn-primary">
        <?php echo Yii::t("language", "Export"); ?>
    </button>
</form>