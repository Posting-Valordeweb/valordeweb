<a href="<?php echo $this->createUrl("admin/category/managetranslations", array("id"=>$trans->category->id)) ?>" class="btn btn-success mb-3">
    <i class="fa fa-language fa-fw"></i> <?php echo Yii::t("language", "Manage Translations") ?>
</a>

<form method="post">

    <?php echo CHtml::errorSummary($trans, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($trans, 'translation'); ?>
        <?php echo CHtml::activeTextField($trans, 'translation', array(
            'class' => 'form-control',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($trans, 'lang_id'); ?>
        <?php echo CHtml::activeDropDownList($trans, 'lang_id', CHtml::listData(Language::model()->getList(false), 'id', array('Language', 'formatLanguage')), array(
            'class' => 'form-control',
            'prompt' => Yii::t("language", "Choose any language"),
        )); ?>
    </div>

    <button class="btn btn-lg btn-primary" type="submit">
        <?php echo $trans->isNewRecord ?  Yii::t("misc", "Create") : Yii::t("misc", "Update"); ?>
    </button>
</form>