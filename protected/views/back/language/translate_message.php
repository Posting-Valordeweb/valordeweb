<div class="btn-group mb-3">
    <a href="<?php echo $this->createUrl("admin/language/messages", array("id"=>$source['category'])) ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage translations in {Category} category", array(
					"{Category}"=>Helper::mb_ucfirst($source['category']),
        )) ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/create-message", array("cat_id"=>$source['category'])) ?>" class="btn btn-success">
        <i class="fa fa-plus"></i> <?php echo Yii::t("language", "Create phrase") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/update-message", array("id"=>$source['id'])) ?>" class="btn btn-secondary">
        <i class="fa fa-pencil"></i> <?php echo Yii::t("language", "Edit phrase") ?>
    </a>
</div>


<table class="table mb-3">
    <thead class="thead-light">
        <tr>
            <th>
                <?php echo Yii::t("language", "Category name") ?>
            </th>
            <th>
                <?php echo Yii::t("language", "Phrase") ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $source['category'] ?></td>
            <td><?php echo $source['message'] ?></td>
        </tr>
    </tbody>
</table>

<form class="mb-3" method="post">
    <?php echo CHtml::errorSummary($model, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($model, 'translation'); ?>
        <?php echo CHtml::activeTextArea($model, 'translation', array(
            'class' => 'form-control',
        )); ?>
    </div>

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

    <?php echo CHtml::activeHiddenField($model, 'id') ?>

    <button type="submit" class="btn btn-primary">
        <?php echo Yii::t("language", "Translate phrase"); ?>
    </button>
</form>

<h3 class="mb-3"><?php echo Yii::t("language", "Existing translations") ?></h3>
<div class="table-responsive">
<table class="table table-hover">
	<thead class="thead-light">
		<tr>
			<th><?php echo Yii::t("language", "Language") ?></th>
			<th><?php echo Yii::t("language", "Translation") ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($langObj as $lang):?>
		<tr>
		<?php if(isset($translations[$lang->id])): ?>
			<td width="20%"><?php echo Language::formatLanguage($lang) ?></td>
			<td><?php echo $translations[$lang->id]['translation'] ?></td>
		<?php else: ?>
			<td colspan="2" class="warning">
				<?php echo Yii::t("language", "Missing translation for {Language}", array(
					"{Language}"=>"<strong>".Language::formatLanguage($lang)."</strong>",
				)) ?>
			</td>
		<?php endif; ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>