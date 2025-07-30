<div class="btn-group">
	<a href="<?php echo $this->createUrl("admin/language/index") ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t('language', 'Manage existing languages') ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-success">
		<i class="fa fa-language fw"></i> <?php echo Yii::t("language", "Manage Translations") ?>
	</a>
</div>
<br/><br/>

<form method="post" class="mb-3">

    <?php echo CHtml::errorSummary($model, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'id'); ?>
        <?php echo CHtml::activeTextField($model, 'id', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'language'); ?>
        <?php echo CHtml::activeTextField($model, 'language', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'enabled'); ?>
        <?php echo CHtml::activeDropDownList($model, 'enabled', array(
            Language::STATUS_ENABLED => Yii::t("admin", "Yes"),
            Language::STATUS_DISABLED => Yii::t("admin", "No"),
        ), array(
            'class' => 'form-control',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeCheckBox($model, 'is_default', array(
            'uncheckValue' => Language::NOTDEFAULT_LANG,
            'value' => Language::DEFAULT_LANG,
        )); ?>
        <label>
            <?php echo CHtml::activeLabel($model, 'is_default'); ?>
        </label>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'copy'); ?>
        <?php echo CHtml::activeDropDownList($model, 'copy', $languages, array(
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

    <button type="submit" class="btn btn-primary"><?php echo Yii::t("misc", "Submit") ?></button>
</form>