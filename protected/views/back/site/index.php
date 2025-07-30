<p>
<?php echo Yii::t("admin", "Welcome back, {Username}", array(
	"{Username}"=>sprintf("<strong>%s</strong>", CHtml::encode(Yii::app()->user->name)),
)) ?>
</p>

<?php if($numOfScam): ?>
<div class="alert alert-warning" role="alert">
<?php echo Yii::t("admin", "There are {NumOfScam} complaints of fraud. {Click here} to take an action.", array(
	"{NumOfScam}"=>'<span class="badge">'.$numOfScam.'</span>',
	"{Click here}"=>'<a href="'.$this->createUrl("admin/scam/index").'" class="alert-link">'.Yii::t("misc", "Click here").'</a>',
)) ?>
</div>
<?php endif; ?>

<?php if($numOfMissingTrans): ?>
<div class="alert alert-warning" role="alert">
<?php echo Yii::t("admin", "We have {NumOfMissingTrans} missing translations. {Click here} to find out more.", array(
	"{NumOfMissingTrans}"=>'<span class="badge">'.$numOfMissingTrans.'</span>',
	"{Click here}"=>'<a href="'.$this->createUrl("admin/language/missing-translation").'" class="alert-link">'.Yii::t("misc", "Click here").'</a>',
)) ?>
</div>
<?php endif; ?>