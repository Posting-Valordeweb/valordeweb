<script type="text/javascript">
$(document).ready(function() {
	$('#chat').jscroll({
		debug: true,
		loadingHtml: '<small><?php echo Yii::t("post", "Loading...") ?></small>&nbsp;<i class="fa fa-spinner fa-spin"></i>'
	});
});
</script>

<div class="btn-group mb-20">
	<a href="<?php echo $this->createUrl("post/index") ?>" class="btn btn-info"><i class="fas fa-chevron-left"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "Back to Inbox") ?></a>
	<?php if($website): ?>
	<a href="<?php echo $this->createUrl("website/show", array("domain"=>$website->domain)) ?>" class="btn btn-success" target="_blank"><i class="fas fa-dollar-sign"></i> &nbsp;&nbsp;<?php echo Yii::t("website", "Estimate Price") ?></a>
	<a href="http://<?php echo $website->domain ?>" class="btn btn-secondary" target="_blank"><i class="fas fa-globe"></i> &nbsp;&nbsp;<?php echo Yii::t("sale", "Visit website") ?></a>
	<?php endif; ?>

	<div class="btn-group dropdown">
		<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
			<i class="fa fa-angle-double-down"></i> &nbsp;&nbsp; <?php echo Yii::t("post", "Action") ?>
			<span class="caret"></span>
		</button>

		<div class="dropdown-menu">
            <a class="dropdown-item" href="<?php echo $this->createUrl("post/block-sender", array("id"=>$header['companion_id'])) ?>">
                <i class="fa fa-ban"></i> &nbsp;&nbsp; <?php echo Yii::t("post", "Block Sender") ?>
            </a>
            <a class="dropdown-item" href="<?php echo $this->createUrl("post/report-scam", array("id"=>$header['companion_id'], "chain_id"=>$header['chain_id'])) ?>">
                <i class="fas fa-exclamation-triangle"></i> &nbsp;&nbsp; <?php echo Yii::t("post", "Scammer!") ?>
            </a>
		</div>
	</div>
</div>


<div class="card">
		<div class="card-header">
            <i class="far fa-comments"></i> <?php echo Yii::t("post", "Chat") ?>
		</div>
		<div class="card-body">
			<ul class="chat" id="chat">
				<?php echo $messages ?>
			</ul>
		</div>
		<div class="panel-footer">
			<div class="input-group" style="width: 100%">
				<?php if($block['external']): ?>
					<p><?php echo Yii::t("post", "This user has blocked you") ?></p>
				<?php elseif($block['internal']): ?>
					<p>
						<?php echo Yii::t("post", "You have blocked this user. {Click here} to unblock", array(
							"{Click here}"=>CHtml::link(Yii::t("misc", "Click here") , array("unblock-sender", "id"=>$companion->id)),
						)) ?>
					</p>
				<?php elseif(!$user->canSendMessage()): ?>
					<p><?php echo Yii::t("post", "You are forbidden to send messages") ?></p>
				<?php else: ?>
					<form method="POST" style="width:100%">

					<?php echo CHtml::errorSummary($form, null, null, array(
						'class' => 'alert alert-danger',
					)); ?>

					<?php echo CHtml::activeTextArea($form, 'message', array(
						'class' => 'form-control',
						'placeholder'=>Yii::t("post", "Type your message here..."),
					)); ?>
					<br/><br/><br/>
					<span class="input-group-btn">
						<button class="btn btn-warning btn-sm" id="btn-chat" type="submit">
							<?php echo Yii::t("post", "Send message"); ?>
						</button>
					</span>
					</form>
				<?php endif; ?>
			</div>
		</div>
</div>