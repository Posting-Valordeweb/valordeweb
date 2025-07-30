<a class="btn btn-primary" href="<?php echo $this->createUrl("admin/category/create"); ?>">
	<i class="fa fa-plus"></i> <?php echo Yii::t("category", "Create Category") ?>
</a>
<br/><br/>

<?php if($categories): ?>
<div class="table-responsive">
	<table class="table table-hover">
		<thead class="thead-light">
			<tr>
				<th><?php echo Yii::t("misc", "ID") ?></th>
				<th><?php echo Yii::t("category", "Category") ?></th>
				<th><?php echo Yii::t("category", "Slug") ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($categories as $category): ?>
			<tr>
				<td><?php echo CHtml::encode($category->id) ?></td>
				<td><?php echo CHtml::encode($category->name) ?></td>
				<td><?php echo CHtml::encode($category->slug) ?></td>
				<td>
					<a href="<?php echo $this->createUrl("admin/category/update", array("id"=>$category->id)) ?>">
                        <i class="fas fa-edit"></i>
						<?php echo Yii::t("misc", "Edit") ?>
					</a>
					&nbsp;&nbsp;&nbsp;
					<a href="<?php echo $this->createUrl("admin/category/managetranslations", array("id"=>$category->id)) ?>">
                        <i class="fas fa-language"></i>
						<?php echo Yii::t("language", "Manage Translations") ?>
					</a>
					&nbsp;&nbsp;&nbsp;
					<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/category/delete", array("id"=>$category->id)) ?>">
                        <i class="fas fa-trash"></i>
						<?php echo Yii::t("misc", "Delete") ?>
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>