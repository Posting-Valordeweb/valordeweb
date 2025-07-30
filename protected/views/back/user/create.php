<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/user/index") ?>" class="btn btn-primary">
        <i class="fa fa-users"></i> <?php echo Yii::t("user", "Manage Users") ?>
    </a>
</div>
<br/><br/>

<?php $this->renderPartial("_form", array(
	"user"=>$user,
	"scenario"=>$scenario,
	"languages"=>$languages,
    "roleList"=>$roleList,
)) ?>