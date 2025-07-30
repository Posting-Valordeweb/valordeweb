<?php foreach($messages as $message):
    $isOwner=$box->isOwnerMessage($message);
    $message['message']=str_replace("\n", "<br>", CHtml::encode($message['message']));
?>
	<?php if($isOwner): ?>
		<li class="right clearfix">
			<span class="chat-img pull-right">
				<div class="circle sender">
                    <div class="user">
                        <small class="owner-icon"><?php echo Yii::t("post", "I'm") ?></small>
                    </div>
                </div>
			</span>
			<div class="chat-body clearfix">
				<div class="header">
					<small class=" text-muted"><i class="far fa-clock"></i>&nbsp;&nbsp;<?php echo Helper::time_elapsed_string($message['sent_date']) ?></small>
					<strong class="pull-right primary-font"><?php echo CHtml::encode($user->username) ?></strong>
				</div>
				<p>
					<?php echo $message['message'] ?>
				</p>
			</div>
		</li>
	<?php else: ?>
		<li class="left clearfix">
		<span class="chat-img pull-left">
            <div class="circle owner">
                <div class="user">
                    <i class="fa fa-user fa-lg owner-icon"></i>
                </div>
            </div>
		</span>
		<div class="chat-body clearfix">
			<div class="header">
				<strong class="primary-font"><?php echo CHtml::encode($companion->username) ?></strong> <small class="pull-right text-muted">
                    <i class="far fa-clock"></i>&nbsp;&nbsp;<?php echo Helper::time_elapsed_string($message['sent_date']) ?></small>
			</div>
			<p>
				<?php echo $message['message'] ?>
			</p>
		</div>
		</li>
	<?php endif; ?>
<?php endforeach; ?>

<?php if($pgNr < $pgCnt): ?>
<li style="display:none"><a href="<?php echo $this->createUrl("post/chain", array("id"=>$header['id'], "page"=>$pgNr+1)) ?>"></a></li>
<?php endif; ?>