<div class="btn-group mb-3">
    <a href="<?php echo $this->createUrl("admin/category/index", array("id"=>$category->id)) ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("category", "Manage Categories") ?>
    </a>
    <?php if(!$category->isNewRecord): ?>
        <a href="<?php echo $this->createUrl("admin/category/managetranslations", array("id"=>$category->id)) ?>" class="btn btn-success">
            <i class="fas fa-language"></i> <?php echo Yii::t("language", "Manage Translations") ?>
        </a>
    <?php endif; ?>
</div>

<form method="post">
    <?php echo CHtml::errorSummary($category, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($category, 'name'); ?>

        <?php echo CHtml::activeTextField($category, 'name', array(
            'class' => 'form-control',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($category, 'slug'); ?>

        <?php echo CHtml::activeTextField($category, 'slug', array(
            'class' => 'form-control',
        )); ?>
    </div>

    <button class="btn btn-primary" type="submit">
        <?php echo $category->isNewRecord ?  Yii::t("misc", "Create") : Yii::t("misc", "Update"); ?>
    </button>
</form>