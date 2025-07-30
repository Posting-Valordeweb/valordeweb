<h2 class="mb-20"><?php echo Yii::t("sale", "Sell Website/Domain") ?></h2>
<?php
$this->widget('application.widgets.RequestFormWidget', array(
	"redirect"=>$this->createUrl("sale/add", array("id"=>"__ID__")),
	'hSize'=>3,
));
?>
<h2 class="mb-20"><?php echo Yii::t("sale", "My Websites/Domains on Sale") ?></h2>

<?php if(empty($dataProvider->data)): ?>
<?php echo Yii::t("sale", "Nothing is sold") ?>.
<?php else: ?>
<div class="table-responsive">
<table class="table table-hover table-bordered table-striped">
<thead>
<th><?php echo Yii::t("misc", "ID") ?></th>
<th><?php echo Yii::t("sale", "Domain/Website") ?></th>
<th><?php echo Yii::t("sale", "Category") ?></th>
<th><?php echo Yii::t("website", "Selling price") ?></th>
<th><?php echo Yii::t("website", "Estimate Price") ?></th>
<th></th>
</thead>
<tbody>
<?php foreach($dataProvider->data as $onSale): ?>
<tr>
<td><?php echo CHtml::encode($onSale->website_id); ?></td>
<td><?php echo CHtml::encode($onSale->website->idn); ?></td>
<td><?php echo CHtml::encode($onSale->category->getTranslation()); ?></td>
<td><?php echo Helper::p($onSale->price); ?></td>
<td><?php echo Helper::p($onSale->website->price); ?></td>
<td>
	<a href="<?php echo $this->createUrl("sale/view", array("id"=>$onSale->website_id)) ?>">
		<?php echo Yii::t("misc", "View") ?>
	</a>
	&nbsp;&nbsp;&nbsp;
	<a href="<?php echo $this->createUrl("sale/edit", array("id"=>$onSale->website_id)) ?>">
		<?php echo Yii::t("misc", "Edit") ?>
	</a>
	&nbsp;&nbsp;&nbsp;
	<a onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>');" href="<?php echo $this->createUrl("sale/remove", array("id"=>$onSale->website_id)) ?>">
		<?php echo Yii::t("sale", "Remove from sale") ?>
	</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="pull-right">
<?php $this -> widget('LinkPager', array(
	'pages' => $dataProvider->getPagination(),
	'htmlOptions' => array(
		'class' => 'pagination flex-wrap',
	),
	'cssFile' => false,
	'header' => '',
	'hiddenPageCssClass' => 'disabled',
	'selectedPageCssClass' => 'active',
    'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
)); ?>
</div>
<div class="clearfix"></div>

<?php endif; ?>