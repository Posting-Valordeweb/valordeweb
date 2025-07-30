<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/user/index") ?>" class="btn btn-primary">
        <i class="fa fa-users"></i> <?php echo Yii::t("user", "Manage Users") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/user/view", array("id"=>$user->id)) ?>" class="btn btn-success">
        <i class="fa fa-info-circle"></i> <?php echo Yii::t("user", "User info") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/user/reset-password", array("id"=>$user->id)) ?>" class="btn btn-warning">
        <i class="fa fa-unlock-alt"></i> <?php echo Yii::t("user", "Reset password") ?>
    </a>
</div>
<br/><br/>

<?php $this->renderPartial("_form", array(
	"user"=>$user,
	"scenario"=>$scenario,
	"languages"=>$languages,
    "roleList"=>$roleList,
)) ?>