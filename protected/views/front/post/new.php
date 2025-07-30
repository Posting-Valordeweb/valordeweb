<div class="btn-group mb-20">
	<a href="<?php echo $this->createUrl("post/index") ?>" class="btn btn-info">
        <i class="fa fa-inbox"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_inbox") ?>
    </a>
	<?php if($website): ?>
	<a href="<?php echo $this->createUrl("website/show", array("domain"=>$website->domain)) ?>" class="btn btn-success" target="_blank">
        <i class="fa fa-dollar-sign"></i> &nbsp;&nbsp;<?php echo Yii::t("website", "Estimate Price") ?>
    </a>
	<a href="http://<?php echo $website->domain ?>" class="btn btn-secondary" target="_blank"><i class="fa fa-globe"></i> &nbsp;&nbsp;<?php echo Yii::t("sale", "Visit website") ?></a>
	<?php endif; ?>
</div>

<h4 class="mb-20"><?php echo CHtml::encode($this->title) ?></h4>
<form method="post">
    <?php echo CHtml::errorSummary($form, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($form, 'subject'); ?>
        <?php echo CHtml::activeTextField($form, 'subject', array(
            'class' => 'form-control',
            'placeholder'=>Yii::t("contact", "Subject"),
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($form, 'message'); ?>
        <?php echo CHtml::activeTextArea($form, 'message', array(
            'class' => 'form-control',
            'placeholder'=>Yii::t("post", "Type your message here..."),
        )); ?>
    </div>

    <?php if($block['external']): ?>
        <p><?php echo Yii::t("post", "This user has blocked you") ?></p>
    <?php elseif($user->id==$sale->user_id): ?>
        <p><?php echo Yii::t("post", "You can't send message to yourself") ?></p>
    <?php elseif(!$user->canSendMessage()): ?>
        <p><?php echo Yii::t("post", "You are forbidden to send messages") ?></p>
    <?php else: ?>
        <button class="btn btn-primary" type="submit">
            <?php echo Yii::t("post", "Send message"); ?>
        </button>
    <?php endif; ?>
</form>