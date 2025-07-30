<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage Translations") ?>
    </a>
    <?php if(!empty($messages)): ?>
    <a href="<?php echo $this->createUrl("admin/language/create-message", array("cat_id"=>$cat_id)) ?>" class="btn btn-success">
        <i class="fa fa-plus"></i> <?php echo Yii::t("language", "Create phrase") ?>
    </a>
    <?php endif; ?>
</div>
<br/><br/>

<?php if(!empty($messages)): ?>
<table class="table table-hover">
	<thead class="thead-light">
		<tr>
			<th><?php echo Yii::t("misc", "ID") ?></th>
			<th><?php echo Yii::t("language", "Category name") ?></th>
			<th><?php echo Yii::t("language", "Phrase") ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($messages as $message): ?>
		<tr>
			<td><?php echo CHtml::encode($message['id']) ?></td>
			<td><?php echo CHtml::encode($message['category']) ?></td>
			<td><?php echo CHtml::encode($message['message']) ?></td>
			<td>
				<a href="<?php echo $this->createUrl("admin/language/translatemessage", array("id"=>$message['id'])) ?>">
					<?php echo Yii::t("language", "Translate phrase") ?>
				</a>
				<br/>
				<a href="<?php echo $this->createUrl("admin/language/updatemessage", array("id"=>$message['id'])) ?>">
					<?php echo Yii::t("misc", "Update") ?>
				</a>
				<br/>
				<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/language/deletemessage", array("id"=>$message['id'])) ?>">
					<?php echo Yii::t("misc", "Delete") ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>