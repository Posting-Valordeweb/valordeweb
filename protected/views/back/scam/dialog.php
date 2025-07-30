<div class="btn-group">
	<a href="<?php echo $backUrl ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("scam", "Scam reports") ?>
	</a>

	<a href="<?php echo $this->createUrl("admin/user/view", array("id"=>$sender->id)) ?>" class="btn btn-success" target="_blank">
		<i class="fa fa-user"></i> <?php echo Yii::t("scam", "Sender") ?>
	</a>
	<a href="<?php echo $this->createUrl("admin/user/view", array("id"=>$scammer->id)) ?>" class="btn btn-danger" target="_blank">
		<i class="fa fa-user"></i> <?php echo Yii::t("scam", "Scammer") ?>
	</a>

	<a href="<?php echo $this->createUrl("admin/scam/restrict-user", array("id"=>$scammer->id)) ?>" class="btn btn-warning">
		<i class="fa fa-minus-circle"></i>
		<?php echo Yii::t("scam", "Prevent {Scammer} from sending letters", array(
			"{Scammer}"=>CHtml::encode($scammer->username),
		)) ?>
	</a>
</div>
<br/><br/>

<h3><?php echo Yii::t("scam", "Legend") ?></h3>

<table class="table table-bordered" style="width:300px !important;">
<tr class="active">
<td style="width:100px !important; word-wrap: break-word;">
<?php echo Yii::t("scam", "Scammer") ?>
</td>
<td>
<strong><?php echo CHtml::encode($scammer->username) ?></strong>
</td>
</tr>

<tr>
<td>
<?php echo Yii::t("scam", "Sender") ?>
</td>
<td>
<strong><?php echo CHtml::encode($sender->username) ?></strong>
</td>
</tr>
</table>

<div class="table-responsive">
<table class="table table-bordered">
	<thead class="thead-light">
		<th><?php echo Yii::t("user", "Username") ?></th>
		<th><?php echo Yii::t("post", "Message") ?></th>
		<th><?php echo Yii::t("scam", "Sent at") ?></th>
	</thead>
	<tbody>
		<?php foreach($messages as $message): $isOwner=$box->isOwnerMessage($message); ?>
		<tr<?php echo !$isOwner ? ' class="active"' : null ?>>
			<td style="width:100px !important; word-wrap: break-word;">
				<?php if($isOwner): ?>
					<strong><?php echo CHtml::encode($sender->username) ?></strong>
				<?php else: ?>
					<strong><?php echo CHtml::encode($scammer->username) ?></strong>
				<?php endif; ?>
			</td>
			<td>
				<?php echo CHtml::encode($message['message']); ?>
			</td>
			<td style="width:200px !important; word-wrap: break-word;">
				<?php echo Yii::app()->dateFormatter->formatDateTime($message['sent_date']) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>


<div class="pull-right">
<?php $this -> widget('LinkPager', array(
	'pages' => $pages,
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