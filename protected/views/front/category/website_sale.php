<script type="text/javascript">
    $(document).ready(function(){
        var urls = {
        <?php foreach($thumbnailStack as $id=>$thumbnail): ?>
        <?php echo $id ?>:<?php echo $thumbnail ?>,
        <?php endforeach; ?>
        };
        dynamicThumbnail(urls);
    });
</script>
<h2><?php echo CHtml::encode($this->title) ?></h2>
<?php if(!empty($categories)): ?>
	<table class="table">
		<tr>
		<?php $i=0; foreach($categories as $category): $url = $this->createUrl("category/index", array(
			"slug" => $category->slug,
		));?>
			<td width="<?php echo round(100 / 3) ?>%" <?php echo $category->id == $activeCat ? 'class="info"' : null ?>>
				<a href="<?php echo $url ?>">
					<?php echo CHtml::encode($category->getTranslation()) ?>
					<?php if($category->onsaleCount): ?>&nbsp;&nbsp;<span class="badge badge-primary"><?php echo $category->onsaleCount ?></span><?php endif ?>
				</a>
			</td>
		<?php if(($i + 1) % 3 == 0) :?></tr><tr><?php endif; ?>
		<?php $i++; endforeach; ?>
		</tr>
	</table>
<?php endif; ?>

<div class="col-xs-12 breadcrumb">


<div class="col-md-4">
<div class="form-group"><label class="control-label"><?php echo $summaryText ?></label></div>
</div>

<div class="col-md-8">
<form class="form-horizontal">
	<div class="form-group">
		<label for="sort-by" class="col-sm-2 control-label"><?php echo Yii::t("category", "Sort by") ?>:</label>
		<div class="col-sm-10">
			<select id="sort-by" class="form-control" onchange="window.location.href=this.value">
				<option value="<?php echo $this->createUrl("category/index",
					array_merge($_GET, array("order"=>"added_at", "sort"=>"asc"))
				) ?>"<?php if($order=="t.added_at" AND $sort=="asc") echo " selected"?>><?php echo Yii::t("category", "Selling date") ?> (<?php echo Yii::t("category", "Ascending order") ?>)</option>
				<option value="<?php echo $this->createUrl("category/index",
					array_merge($_GET, array("order"=>"added_at", "sort"=>"desc"))
				) ?>"<?php if($order=="t.added_at" AND $sort=="desc") echo " selected"?>><?php echo Yii::t("category", "Selling date") ?> (<?php echo Yii::t("category", "Descending order") ?>)</option>
				<option value="<?php echo $this->createUrl("category/index",
					array_merge($_GET, array("order"=>"price", "sort"=>"asc"))
				) ?>"<?php if($order=="t.price" AND $sort=="asc") echo " selected"?>><?php echo Yii::t("website", "Selling price") ?> (<?php echo Yii::t("category", "Ascending order") ?>)</option>
				<option value="<?php echo $this->createUrl("category/index",
					array_merge($_GET, array("order"=>"price", "sort"=>"desc"))
				) ?>"<?php if($order=="t.price" AND $sort=="desc") echo " selected"?>><?php echo Yii::t("website", "Selling price") ?> (<?php echo Yii::t("category", "Descending order") ?>)</option>
			</select>
		</div>
	</div>
</form>
</div>
</div>

<div class="clearfix"></div>
<?php foreach($data as $onSale):
$url=$this->createUrl("website/show", array("domain"=>$onSale->website->domain));
?>
<div class="row">
	<div class="col-md-3 col-sm-4">
		<a href="<?php echo $url ?>">
			<img class="img-responsive img-thumbnail" id="thumb_<?php echo $onSale->website->id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $onSale->website->idn ?>">
		</a>
	</div>
	<div class="col-md-9 col-sm-8 text-left">
		<h3 class="no-top-margin"><a href="<?php echo $url ?>"><?php echo $onSale->website->idn?></a></h3>
		<p>
			<i class="fa fa-quote-left fa-3x pull-left fa-border mr-3"></i>
			<?php echo Helper::mb_ucfirst(Helper::cropText(CHtml::encode($onSale->description), 50)); ?>
		</p>
		<div class="clearfix"></div>

		<br/>
		<span class="badge badge-warning"><?php echo Yii::t("website", "Unique monthly visitors") ?>: <strong><?php echo CHtml::encode(Helper::f($onSale->monthly_visitors)) ?></strong></span>&nbsp;
		<span class="badge badge-primary"><?php echo Yii::t("website", "Monthly revenue") ?>: <strong><?php echo Helper::p($onSale->monthly_revenue) ?></strong></span>&nbsp;
		<span class="badge badge-secondary"><?php echo Yii::t("website", "Monthly page view") ?>: <strong><?php echo CHtml::encode(Helper::f($onSale->monthly_views)) ?></strong></span>&nbsp;
        <?php /*
		<span class="badge badge-success"><?php echo Yii::t("website", "Page Rank") ?>: <strong><?php echo CHtml::encode($onSale->website->search_engine->page_rank) ?></strong></span>&nbsp;
        <span class="badge badge-info"><?php echo Yii::t("website", "Alexa Rank") ?>: <strong><?php echo CHtml::encode(Helper::f($onSale->website->alexa->rank)) ?></strong></span>&nbsp;
        */ ?>
		<br/><br/>
		<table class="table custom-border">
			<tr>
				<td width="150px"><?php echo Yii::t("website", "Estimate Price") ?>:</td>
				<td><strong><?php echo Helper::p($onSale->website->price) ?></strong></td>
			</tr>
			<tr>
				<td><?php echo Yii::t("website", "Selling price") ?>:</td>
				<td><strong><?php echo Helper::p($onSale->price) ?></strong></td>
			</tr>
		</table>
		<small class="pull-left"><?php echo Yii::t("category", "Is sold since") ?>: <?php echo Yii::app()->dateFormatter->formatDateTime($onSale->added_at, 'long', 'medium');?></small>
		<a class="btn btn-primary btn-sm pull-right" href="<?php echo $url ?>"><?php echo Yii::t("website", "Explore more") ?></a>
	</div>
</div>
<hr>
<?php endforeach; ?>

<div class="pull-right">
<?php $this -> widget('LinkPager', array(
	'pages' => $pagination,
	'htmlOptions' => array(
		'class' => 'pagination flex-wrap',
	),
	'cssFile' => false,
	'header' => '',
	'hiddenPageCssClass' => 'disabled',
	'selectedPageCssClass' => 'active',
    'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
)); ?>
</div>
<div class="clearfix"></div>