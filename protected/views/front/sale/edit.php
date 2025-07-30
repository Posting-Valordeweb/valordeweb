<script type="text/javascript">
	$(document).ready(function(){
		dynamicThumbnail({<?php echo $onSale->website_id ?>:<?php echo $thumbnail ?>});
	});
</script>

<h1 class="mb-20"><?php echo CHtml::encode($this->title) ?></h1>

<div class="jumbotron">
    <div class="row">
        <div class="col-xs-6 col-md-4">
            <img class="img-thumbnail img-responsive" id="thumb_<?php echo $onSale->website_id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $onSale->website->idn ?>" />
        </div>

        <div class="col-xs-6 col-md-8">
            <div class="btn-group-vertical">
                <a class="btn btn-sm btn-success" href="<?php echo $this->createUrl("sale/view", array("id"=>$onSale->website_id)) ?>">
                    <i class="fa fa-file-o"></i>&nbsp;
                    <?php echo Yii::t("misc", "View") ?>
                </a>
                <a class="btn btn-sm btn-warning" href="<?php echo $this->createUrl("sale/remove", array("id"=>$onSale->website_id)) ?>" onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>')">
                    <i class="fa fa-trash-o"></i>&nbsp;
                    <?php echo Yii::t("sale", "Remove from sale") ?>
                </a>
                <a class="btn btn-sm btn-primary" target="_blank" href="<?php echo $this->createUrl("website/show", array("domain"=>$onSale->website->domain)) ?>">
                    <i class="fa fa-usd"></i>&nbsp;
                    <?php echo Yii::t("website", "Estimate Price") ?>
                </a>
                <a class="btn btn-sm btn-default" target="_blank" href="http://<?php echo $onSale->website->domain ?>">
                    <i class="fa fa-globe"></i>&nbsp;
                    <?php echo Yii::t("sale", "Visit website") ?>
                </a>
            </div>
        </div>
    </div>

</div>

<a href="<?php echo $this->createUrl("sale/index") ?>" class="btn btn-primary btn-md">
    <i class="fas fa-chevron-left"></i>&nbsp;
	<?php echo Yii::t("sale", "My Websites/Domains on Sale") ?>
</a>
<br/><br/>

<?php $this->renderPartial("_form", array(
	"onSale"=>$onSale,
	"catList"=>$catList,
)) ?>