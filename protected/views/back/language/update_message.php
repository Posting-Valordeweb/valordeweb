<div class="btn-group mb-3">
    <a href="<?php echo $this->createUrl("admin/language/messages", array("id"=>$source['category'])) ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage translations in {Category} category", array(
					"{Category}"=>Helper::mb_ucfirst($source['category']),
        )) ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/translate-message", array("id"=>$source['id'])) ?>" class="btn btn-success">
        <i class="fa fa-language"></i> <?php echo Yii::t("language", "Translate phrase") ?>
    </a>
</div>


<form class="mb-3" method="post">
    <?php echo CHtml::errorSummary($model, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'message'); ?>

        <?php echo CHtml::activeTextField($model, 'message', array(
            'class' => 'form-control',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'category'); ?>

        <?php echo CHtml::activeDropDownList($model, 'category', $categories, array(
            'class' => 'form-control',
            'options' => array(
                '-' => array(
                    'disabled' => 'disabled',
                ),
                '' => array(
                    'readonly' => 'readonly',
                ),
        ))); ?>
    </div>

    <div class="form-group">
        <?php echo Yii::t("misc", "OR") ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'new_category'); ?>
        <?php echo CHtml::activeTextField($model, 'new_category', array(
            'class' => 'form-control',
        )); ?>
    </div>

    <button type="submit" class="btn btn-primary">
        <?php echo Yii::t("misc", "Update"); ?>
    </button>
</form>