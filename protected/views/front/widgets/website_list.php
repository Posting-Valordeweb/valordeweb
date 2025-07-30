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

<div class="row">
<?php
	foreach ($data as $website):
		$url = Yii::app()->controller->createUrl("website/show", array("domain"=>$website->domain));
?>
    <div class="col col-12 col-md-6 col-lg-4 mb-4">
        <div class="card mb-3">
            <h3 class="card-header"><?php echo Helper::cropDomain($website->idn) ?></h3>
            <a href="<?php echo $url ?>">
                <img class="card-img-top" id="thumb_<?php echo $website->id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $website->idn ?>" />
            </a>
            <div class="card-body">
                <p class="card-text">
                    <?php echo Yii::t("website", "Estimate Price") ?>: <strong><?php echo Helper::p($website->price) ?></strong>
                </p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?php echo Helper::getMaxLabel(
                        $website->search_engine->google_index,
                        $website->search_engine->bing_index,
                        $website->search_engine->yahoo_index,
                        $website->search_engine->google_backlinks
                    ) ?>: <span class="badge badge-success card-badge"><?php echo Helper::f(max(
                        $website->search_engine->google_index,
                        $website->search_engine->bing_index,
                        $website->search_engine->yahoo_index,
                        $website->search_engine->google_backlinks
                    )) ?></span>
                </li>
                <li class="list-group-item">
                    Facebook: <span class="badge badge-success card-badge"><?php echo Helper::f($website->social->facebook_total_count) ?></span>
                </li>
                <li class="list-group-item">
                    <?php echo Yii::t("website", "Norton") ?>:<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/<?php echo $website->antivirus->avg ?>.png" alt="<?php echo $website->antivirus->avg ?>" class="badge-icon">
                </li>
            </ul>
            <div class="card-body">
                <a class="btn btn-primary" href="<?php echo $url ?>">
                    <?php echo Yii::t("website", "Explore more") ?>
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php $this -> widget('LinkPager', array(
	'pages' => $dataProvider->getPagination(),
	'htmlOptions' => array(
		'class' => 'pagination flex-wrap',
	),
	'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
	'cssFile' => false,
	'header' => '',
	'hiddenPageCssClass' => 'disabled',
	'selectedPageCssClass' => 'active',
)); ?>

<div class="clearfix"></div>