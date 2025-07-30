<div class="btn-group mb-3">
	<a href="<?php echo $this->createUrl("admin/language/index") ?>" class="btn btn-primary">
		<i class="fa fa-list"></i> <?php echo Yii::t('language', 'Manage existing languages') ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/create-message") ?>" class="btn btn-success">
		<i class="fa fa-plus"></i> <?php echo Yii::t("language", "Create phrase") ?>
	</a>

	<?php if($missingCnt): ?>
	<a href="<?php echo $this->createUrl("admin/language/missing-translation") ?>" class="btn btn-danger">
		<i class="fa fa-question-circle"></i> <?php echo Yii::t("language", "Missing translations") ?> <span class="badge"><?php echo $missingCnt ?></span>
	</a>
	<?php endif; ?>

	<a href="<?php echo $this->createUrl("admin/language/search") ?>" class="btn btn-info">
		<i class="fa fa-search"></i> <?php echo Yii::t("language", "Find phrase/translation") ?>
	</a>

	<a href="<?php echo $this->createUrl("admin/language/export") ?>" class="btn btn-secondary">
		<i class="fa fa-download"></i> <?php echo Yii::t("language", "Export Translation") ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/language/import") ?>" class="btn btn-warning">
		<i class="fa fa-upload"></i> <?php echo Yii::t("language", "Import Translation") ?>
	</a>
</div>

<?php if($categories): ?>
<table class="table table-hover mb-3">
	<thead class="thead-light">
		<tr>
			<th><?php echo Yii::t("language", "Category name") ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($categories as $category): ?>
		<tr>
			<td><?php echo CHtml::encode($category) ?></td>
			<td>
				<a href="<?php echo $this->createUrl("admin/language/categoryupdate", array("id"=>$category)) ?>">
					<?php echo Yii::t("misc", "Edit") ?>
				</a>
				&nbsp;&nbsp;&nbsp;
				<a href="<?php echo $this->createUrl("admin/language/messages", array("id"=>$category)) ?>">
					<?php echo Yii::t("language", "Manage Translations") ?>
				</a>
				&nbsp;&nbsp;&nbsp;
				<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/language/deletecategory", array("id"=>$category)) ?>">
					<?php echo Yii::t("misc", "Delete") ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>