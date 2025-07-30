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
                <a class="btn btn-sm btn-success" href="<?php echo $this->createUrl("sale/edit", array("id"=>$onSale->website_id)) ?>">
                    <i class="fa fa-edit"></i>&nbsp;
                    <?php echo Yii::t("misc", "Edit") ?>
                </a>
                <a class="btn btn-sm btn-warning" href="<?php echo $this->createUrl("sale/remove", array("id"=>$onSale->website_id)) ?>" onclick="return confirm('<?php echo Yii::t("misc", "Are you sure you want to delete this item?") ?>')">
                    <i class="fas fa-trash"></i>&nbsp;
                    <?php echo Yii::t("sale", "Remove from sale") ?>
                </a>
                <a class="btn btn-sm btn-primary" target="_blank" href="<?php echo $this->createUrl("website/show", array("domain"=>$onSale->website->domain)) ?>">
                    <i class="fas fa-dollar-sign"></i>&nbsp;
                    <?php echo Yii::t("website", "Estimate Price") ?>
                </a>
                <a class="btn btn-sm btn-secondary" target="_blank" href="http://<?php echo $onSale->website->domain ?>">
                    <i class="fas fa-globe"></i>&nbsp;
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

<table class="table table-hover table-bordered table-striped">
<thead>
<th><?php echo Yii::t("sale", "Field") ?></th>
<th><?php echo Yii::t("sale", "Value") ?></th>
</thead>
<tbody>
<td><?php echo Yii::t("sale", "Domain/Website") ?></td>
<td><?php echo CHtml::encode($onSale->website->domain); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("sale", "Category") ?></td>
<td><?php echo CHtml::encode($onSale->category->getTranslation()); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Selling price") ?></td>
<td><?php echo Helper::p($onSale->price); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Estimate Price") ?></td>
<td><?php echo Helper::p($onSale->website->price); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Unique monthly visitors") ?></td>
<td><?php echo CHtml::encode(Helper::f($onSale->monthly_visitors)); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Monthly revenue") ?></td>
<td><?php echo Helper::p($onSale->monthly_revenue); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Monthly page view") ?></td>
<td><?php echo CHtml::encode(Helper::f($onSale->monthly_views)); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("website", "Notes") ?></td>
<td><?php echo CHtml::encode($onSale->description); ?></td>
</tr>
<tr>
<td><?php echo Yii::t("category", "Is sold since") ?></td>
<td><?php echo Yii::app()->dateFormatter->formatDateTime($onSale->added_at, 'long', 'medium'); ?></td>
</tr>
</tbody>
</table>