<a class="btn btn-primary" href="<?php echo $this->createUrl("admin/scam/flush"); ?>">
<?php echo Yii::t("misc", "Remove all records") ?>
</a>
<br/><br/>

<div class="table-responsive">
	<table class="table table-hover">
		<thead class="thead-light">
			<tr>
				<th><?php echo Yii::t("scam", "Sender") ?></th>
				<th><?php echo Yii::t("scam", "Scammer") ?></th>
				<th><?php echo Yii::t("scam", "Complain date") ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($data as $scam): ?>
				<tr>
					<td>
						<a href="<?php echo $this->createUrl("admin/user/view", array("id"=>$scam['sender_id'])) ?>" target="_blank">
							<?php echo CHtml::encode($users[$scam['sender_id']]->username) ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $this->createUrl("admin/user/view", array("id"=>$scam['scammer_id'])) ?>" target="_blank">
							<?php echo CHtml::encode($users[$scam['scammer_id']]->username) ?>
						</a>
					</td>
					<td>
						<?php echo Yii::app()->dateFormatter->formatDateTime($scam['complain_date']) ?>
					</td>
					<td>
						<a class="btn btn-info" href="<?php echo $this->createUrl("admin/scam/dialog", array(
							"chain_id"=>$scam['chain_id'],
							"sender_id"=>$scam['sender_id'],
						)) ?>"><?php echo Yii::t("scam", "View dialog") ?></a><br/>
						<a class="btn btn-warning" href="<?php echo $this->createUrl("admin/scam/remove", array(
							"sender_id"=>$scam['sender_id'],
							"scammer_id"=>$scam['scammer_id'],
						)) ?>"><?php echo Yii::t("misc", "Delete") ?></a><br/>
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