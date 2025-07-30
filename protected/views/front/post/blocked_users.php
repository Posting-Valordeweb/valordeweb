<h1 class="mb-20"><?php echo CHtml::encode($this->title) ?></h1>

<div class="btn-group btn-group-md mb-20">
	<a href="<?php echo $this->createUrl("post/index") ?>" class="btn btn-info"><i class="fa fa-inbox"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "Back to Inbox") ?></a>
</div>

<table class="table table-hover">
	<thead class="thead-light">
		<tr>
			<th><?php echo Yii::t("user", "Username") ?></th>
			<th><?php echo Yii::t("post", "Blocked at") ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($blocked as $id=>$block): ?>
		<tr>
			<td>
				<?php echo CHtml::encode($users[$id]->username) ?>
			</td>

			<td>
				<?php echo Yii::app()->dateFormatter->formatDateTime($block['block_date'], 'long', 'medium'); ?>
			</td>

			<td>
				<a class="btn btn-sm btn-primary" href="<?php echo $this->createUrl("post/unblock-sender", array("id"=>$id)) ?>"><?php echo Yii::t("post", "Unblock") ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<thead>
		<tr>
				<th colspan="3" class="text-center">
						<a href="<?php echo $this->createUrl("post/blocked-users", array("page"=>$pgNr-1)) ?>" class="btn btn-sm btn-primary<?php echo ($pgNr<=1) ? " disabled" : null?>">
								<i class="fa fa-angle-left"></i>
						</a>
						<a href="<?php echo $this->createUrl("post/blocked-users", array("page"=>$pgNr+1)) ?>" class="btn btn-sm btn-primary<?php echo ($pgNr>=$pgCnt) ? " disabled" : null?>">
								<i class="fa fa-angle-right"></i>
						</a>
						<span class="pull-right"><?php echo $summaryText ?></span>
				</th>
		</tr>
	</thead>
</table>