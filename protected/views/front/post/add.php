<script type="text/javascript">
$(document).ready(function() {
	<?php if($website): ?>
	dynamicThumbnail({<?php echo $website->id ?>:<?php echo WebsiteThumbnail::getThumbData(array("url"=>$website->domain)) ?>});
	<?php endif; ?>
});
</script>

<div class="row mb-20">
    <div class="col-lg-8">
        <?php echo $widget ?>
    </div>
    <div class="col-lg-4">
        <div class="card bg-<?php echo $website ? "primary" : "warning" ?>">
            <div class="card-header text-white">
                <span class="fa-stack">
                <i class="fa fa-globe fa-stack-1x"></i>
                <?php if(!$website): ?>
                <i class="fa fa-ban fa-stack-2x text-danger"></i>
                <?php endif; ?>
                </span>
                &nbsp;<?php echo $website ? $website->idn : Yii::t("post", "Removed from sale"); ?>
            </div>
            <?php if($website): ?>
                <img id="thumb_<?php echo $website->id ?>" class="card-img-top" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $website->idn ?>">
            <?php endif; ?>
            <div class="card-body bg-white">
                <?php if(!$website): ?>
                <p class="card-text"><?php echo Yii::t("post", "The website has been sold or removed from sales.") ?></p>
                <?php else: ?>
                <table class="table custom-border">
                        <tr>
                                <td width="30%">
                                        <?php echo Yii::t("website", "Estimate Price"); ?>
                                </td>
                                <td>
                                        <strong><?php echo Helper::p($website->price) ?></strong>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo Yii::t("website", "Selling price") ?>
                                </td>
                                <td>
                                        <strong><?php echo Helper::p($sale->price) ?></strong>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo Yii::t("website", "Unique monthly visitors") ?>
                                </td>
                                <td>
                                        <strong><?php echo Helper::f($sale->monthly_visitors) ?></strong>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo Yii::t("website", "Monthly page view") ?>
                                </td>
                                <td>
                                        <strong><?php echo Helper::f($sale->monthly_views) ?></strong>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <?php echo Yii::t("website", "Monthly revenue") ?>
                                </td>
                                <td>
                                        <strong><?php echo Helper::p($sale->monthly_revenue) ?></strong>
                                </td>
                        </tr>
                </table>
                    <h4><?php echo Yii::t("website", "Notes") ?></h4>
                    <p class="break-word"><?php echo Helper::mb_ucfirst(CHtml::encode($website->sale->description)) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
