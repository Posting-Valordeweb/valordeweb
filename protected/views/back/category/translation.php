<div class="btn-group">
<a class="btn btn-success" href="<?php echo $this->createUrl("admin/category/index"); ?>">
    <i class="fas fa-chevron-left"></i> <?php echo Yii::t("category", "Manage Categories") ?>
</a>
<a class="btn btn-primary" href="<?php echo $this->createUrl("admin/category/createtranslation", array("id"=>$category->id)); ?>">
    <i class="fas fa-language"></i> <?php echo Yii::t("category", "Create translation") ?>
</a>
</div>
<br/><br/>

<div class="table-responsive">
	<table class="table table-hover">
		<thead class="thead-light">
			<tr>
				<th><?php echo Yii::t("category", "Category") ?></th>
				<th><?php echo Yii::t("language", "Language ID") ?></th>
				<th><?php echo Yii::t("language", "Translation") ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach (Language::model()->getList(false) as $language): ?>
			<?php if(!$category->hasTranslation($language->id)): ?>
				<tr class="warning">
					<td colspan="3" class="warning">
						<?php echo Yii::t("language", "Missing translation for {Language}", array(
							"{Language}"=>"<strong>".Language::formatLanguage($language)."</strong>",
						)) ?>
					</td>
					<td>
						<a href="<?php echo $this->createUrl("admin/category/createtranslation", array("id"=>$category->id, "lang_id"=>$language->id)) ?>">
							<?php echo Yii::t("category", "Create translation") ?>
						</a>
					</td>
				</tr>
			<?php else: $translation=$category->getTranslationObject($language->id); ?>
			<tr>
				<td><?php echo CHtml::encode($category->name) ?></td>
				<td><?php echo CHtml::encode($language->id) ?></td>
				<td><?php echo CHtml::encode($translation->translation) ?></td>
				<td>
					<a href="<?php echo $this->createUrl("admin/category/updatetranslation", array("id"=>$translation->id)) ?>">
                        <i class="fas fa-edit"></i>
						<?php echo Yii::t("misc", "Edit") ?>
					</a>
					&nbsp;&nbsp;&nbsp;
					<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/category/deletetranslation", array("id"=>$translation->id, "cat_id"=>$category->id)) ?>">
                        <i class="fas fa-trash"></i>
						<?php echo Yii::t("misc", "Delete") ?>
					</a>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>