<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage Translations") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/search") ?>" class="btn btn-secondary" target="_blank">
        <i class="fa fa-search"></i> <?php echo Yii::t("language", "Find phrase/translation") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/language/truncate-missing-translation") ?>" class="btn btn-success">
        <i class="fa fa-trash"></i> <?php echo Yii::t("misc", "Remove all records") ?>
    </a>
</div>
<br/><br/>


<div class="table-responsive">
<table class="table table-hover">
	<thead class="thead-light">
		<tr>
			<th><?php echo Yii::t("language", "Category name") ?></th>
			<th><?php echo Yii::t("language", "Language ID") ?></th>
			<th><?php echo Yii::t("language", "Phrase") ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($dataProvider->getData() as $result): ?>
		<tr>
			<td><?php echo CHtml::encode($result['category']) ?></td>
			<td><?php echo CHtml::encode($result['lang_id']) ?></td>
			<td><?php echo CHtml::encode($result['key']) ?></td>
			<td>
				<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("admin/language/delete-missing-translation", array("id"=>$result['id'])) ?>">
					<?php echo Yii::t("misc", "Delete") ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>

<?php $this->widget('CLinkPager', array(
	'pages' => $dataProvider->getPagination(),
	'cssFile' => false,
	'header' => '',
	'hiddenPageCssClass' => 'disabled',
	'selectedPageCssClass' => 'active',
	'htmlOptions' => array(
		'class' => 'pagination pagination-sm flex-wrap',
	),
    'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
)); ?>