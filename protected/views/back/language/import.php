<div class="btn-group mb-3">
	<a href="<?php echo $this->createUrl("admin/language/index") ?>" class="btn btn-primary">
		<i class="fa fa-list"></i> <?php echo Yii::t('language', 'Manage existing languages') ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-success">
		<i class="fa fa-language"></i> <?php echo Yii::t("language", "Manage Translations") ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/export") ?>" class="btn btn-warning">
		<i class="fa fa-download"></i> <?php echo Yii::t("language", "Export Translation") ?>
	</a>
</div>


<form enctype="multipart/form-data" method="post">
    
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
        <div class="custom-file">
            <?php echo CHtml::activeFileField($model, 'zip', array(
                'class' => 'form-control',
            )); ?>
            <?php echo CHtml::activeLabel($model, 'zip', array(
                'class'=>'custom-file-label'
            )); ?>
        </div>
        <p class="help-block"><?php echo Yii::t("language", "You are able upload only ZIP file") ?></p>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeCheckBox($model, 'force') ?>
        <label for="ExportTranslationForm_force" data-toggle="tooltip" title="<?php echo Yii::t("language", "This means that if there is a phrase translation it will be replaced") ?>" data-placement="right">
            <?php echo CHtml::encode($model->getAttributeLabel('force')) ?>
        </label>
    </div>

    <button type="submit" class="btn btn-primary" clas>
        <?php echo Yii::t("language", "Import"); ?>
    </button>

</form>