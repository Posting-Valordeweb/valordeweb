<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/language/create") ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> <?php echo Yii::t("language", "Create Language") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-success">
        <i class="fa fa-language"></i> <?php echo Yii::t("language", "Manage Translations") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/export") ?>" class="btn btn-secondary">
        <i class="fa fa-download"></i> <?php echo Yii::t("language", "Export Translation") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/import") ?>" class="btn btn-warning">
        <i class="fa fa-upload"></i> <?php echo Yii::t("language", "Import Translation") ?>
    </a>
</div>
<br/><br/>

<div class="table-responsive">
	<table class="table table-hover">
		<thead class="thead-light">
			<tr>
				<th><?php echo Yii::t("language", "Language ID") ?></th>
				<th><?php echo Yii::t("language", "Language") ?></th>
				<th><?php echo Yii::t("language", "Is Enabled") ?></th>
				<th><?php echo Yii::t("language", "Is Default") ?></th>
				<th><?php echo Yii::t("language", "Created at") ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($languages as $language): ?>
			<tr<?php echo !$language->isEnabled() ? ' class="warning"': null ?>>
				<td><?php echo CHtml::encode($language->id) ?></td>
				<td><?php echo CHtml::encode($language->language) ?></td>
				<td><?php echo $language->isEnabled() ? Yii::t("admin", "Yes") : Yii::t("admin", "No") ?></td>
				<td><?php echo $language->isDefault() ? Yii::t("admin", "Yes") : Yii::t("admin", "No") ?></td>
				<td><?php echo Yii::app()->dateFormatter->formatDateTime($language->created_at, 'medium', null) ?></td>
				<td>
					<a href="<?php echo $this->createUrl("admin/language/update", array("id"=>$language->id)) ?>">
						<?php echo Yii::t("misc", "Edit") ?>
					</a>
					<?php if(!$language->isDefault()): ?>
					&nbsp;&nbsp;&nbsp;
					<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/language/delete", array("id"=>$language->id)) ?>">
						<?php echo Yii::t("misc", "Delete") ?>
					</a>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>