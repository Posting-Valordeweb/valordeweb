<script type="text/javascript">
    $(document).ready(function(){
        dynamicThumbnail({<?php echo "main_".$website->id ?>:<?php echo $thumbnail ?>});
    });
</script>

<h1 class="mb-20 break-word">
<?php echo Yii::t("website", "How much {Website} is worth?", array(
	"{Website}"=>$website->idn,
))?>
</h1>

<div class="jumbotron">
    <div class="row">
        <div class="col-md-4 col-lg-6 col-sm-12">
            <img class="img-responsive img-thumbnail mb-20" id="thumb_main_<?php echo $website->id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $website->idn ?>" />
        </div>
        <div class="col-md-8 col-lg-6 col-sm-12 text-left">
            <h2 class="break-word"><?php echo $website->idn ?></h2>
            <p><?php echo Yii::t("website", "Has Estimated Worth of") ?></p>
            <h2>
                <span class="badge badge-success"><?php echo Helper::p($website->price) ?></span>
                <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/coins.png" alt="<?php echo Yii::t("website", "Coins") ?>">
            </h2>
            <br/>
            <small>
                <?php echo Yii::t("website", "Site Price calculated at: {Time}", array(
                    "{Time}"=>$time,
                )); ?>
                <?php if($update): ?>
                &nbsp;&nbsp;
                <?php if(!Helper::isAllowedCaptcha()) : ?>
                <a href="<?php echo $updateLink ?>"><i class="fas fa-sync-alt fa-lg"></i></a>
                <?php else: ?>
                <a href="#domain_name" id="recalculate"><i class="fas fa-sync-alt fa-lg"></i></a>
                <script type="text/javascript">
                $("#recalculate").on("click", function() {
                    $("#domain_name").val("<?php echo $website->idn ?>");
                });
                </script>
                <?php endif; ?>
                <?php endif; ?>
            </small>
            <p>
                <br/>
                <?php echo Yii::app()->params["share.js"] ."\n". Yii::app()->params["share.html"] ?>
            </p>
        </div>
    </div>
</div>

<div class="clearfix"></div>

<div class="card mb-20">
	<div class="card-header">
        <i class="fas fa-cogs"></i>
        &nbsp;
        <?php echo Yii::t("website", "More actions") ?>
	</div>
	<div class="card-body">
        <div class="btn-group flex-wrap">
            <a class="btn btn-sm btn-success" href="http://website-review.php8developer.com" title="" target="_blank">
                <i class="fa fa-list-alt"></i>&nbsp;
                <?php echo Yii::t("website", "Get website review") ?>
            </a>
            <a class="btn btn-sm btn-info" href="http://webmaster-tools.php8developer.com" title="" target="_blank">
                <i class="fas fa-cogs"></i>&nbsp;
                <?php echo Yii::t("website", "Webmaster info") ?>
            </a>
            <a class="btn btn-sm btn-warning" href="http://catalog.php8developer.com" title="" target="_blank">
                <i class="fa fa-folder-open"></i>&nbsp;
                <?php echo Yii::t("website", "Add to catalog. Free") ?>
            </a>
            <a class="btn btn-sm btn-primary" href="<?php echo $this->createUrl("website/show", array("domain"=>$website->domain, "#"=>"widget")) ?>">
                <i class="fa fa-share-alt"></i>&nbsp;
                <?php if(Yii::app()->params['app.allow_user_auth']): ?>
                    <?php echo Yii::t("website", "Get widget / Sale website") ?>
                <?php else: ?>
                    <?php echo Yii::t("website", "Get widget") ?>
                <?php endif; ?>
            </a>
            <a class="btn btn-sm btn-secondary" href="<?php echo $this->createUrl("website/show", array("domain"=>$website->domain, "#"=>"estimate")) ?>">
                <i class="fa fa-fax"></i>&nbsp;
                <?php echo Yii::t("website", "Estimate other website") ?>
            </a>
        </div>
	</div>
</div>

<?php if($website->sale AND Yii::app()->params['app.allow_user_auth']): ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-20">
            <strong class="on-sale">&nbsp;<?php echo Yii::t("website", "This website is on sale") ?> </strong><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/sale.png" alt="<?php echo Yii::t("website", "This website is on sale") ?>">
        </h2>
    </div>
</div>

<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/bank.png" alt="<?php echo Yii::t("website", "Sales Information") ?>">
        &nbsp;
        <?php echo Yii::t("website", "Sales Information") ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table custom-border">
                    <tr>
                        <td class="width30pers">
                            <?php echo Yii::t("website", "Selling price") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::p($website->sale->price) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Unique monthly visitors") ?>
                        </td>
                        <td>
                            <strong><?php echo CHtml::encode(Helper::f($website->sale->monthly_visitors)) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Monthly page view") ?>
                        </td>
                        <td>
                            <strong><?php echo CHtml::encode(Helper::f($website->sale->monthly_views)) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Monthly revenue") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::p($website->sale->monthly_revenue) ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                <h4><i class="fa fa-quote-left pull-left fa-border fa-lg"></i>&nbsp;&nbsp;<?php echo Yii::t("website", "Notes") ?></h4>

                <p class="break-word mt-20">
                    <?php echo Helper::mb_ucfirst(CHtml::encode($website->sale->description)) ?>
                </p>
            </div>
        </div>

        <?php if(Yii::app()->user->isGuest): ?>
        <br/>
				<div class="alert alert-success">
					<?php echo Yii::t("website", "You should login to contact seller") ?>
				</div>
        <?php elseif($block AND $block['external']): ?>
					<div class="alert alert-danger">
						<?php echo Yii::t("website", "Seller has blocked you") ?>
					</div>
        <?php elseif($block AND $block['internal']): ?>
					<div class="alert alert-danger">
					<?php echo Yii::t("website", "You have blocked this user. {Click here} to unblock", array(
						"{Click here}"=>CHtml::link(Yii::t("misc", "Click here"), $this->createUrl("post/unblock-sender", array("id"=>$website->sale->user->id))),
					)) ?>
					</div>
        <?php elseif(Yii::app()->user->id != $website->sale->user_id AND Yii::app()->user->loadModel()->canSendMessage()): ?>
            <a class="btn btn-primary" href="<?php echo $this->createUrl("post/send", array("id"=>$website->id)) ?>">
							<?php echo Yii::t("website", "Contact seller") ?>
						</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/analytics.png" alt="<?php echo Yii::t("website", "Estimated Data Analytics") ?>">
        &nbsp;
        <?php echo Yii::t("website", "Estimated Data Analytics") ?>
    </div>
    <div class="card-body">
			<div class="row">
				<div class="col-md-4">
					<table class="table custom-border">
						<thead>
							<tr>
								<th colspan="2">
									<?php echo Yii::t("website", "Estimated Daily Stats") ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/visitors.png" alt="<?php echo Yii::t("website", "Daily Unique Visitors") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Daily Unique Visitors") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $dailyVisitors ? Helper::f($dailyVisitors) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/pageviews.png" alt="<?php echo Yii::t("website", "Daily Pageviews") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Daily Pageviews") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $dailyPageviews ? Helper::f($dailyPageviews) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/revenue.png" alt="<?php echo Yii::t("website", "Daily Ads Revenue") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Daily Ads Revenue") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $dailyAdsRevenue ? Helper::formatCurrencyPrice($dailyAdsRevenue) : "n/a" ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-4">
					<table class="table custom-border">
						<thead>
							<tr>
								<th colspan="2">
									<?php echo Yii::t("website", "Estimated Monthly Stats") ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/visitors.png" alt="<?php echo Yii::t("website", "Estimated Monthly Stats") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Monthly Unique Visitors") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $monthlyVisitors ? Helper::f($monthlyVisitors) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/pageviews.png" alt="<?php echo Yii::t("website", "Monthly Pageviews") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Monthly Pageviews") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $monthlyPageviews ? Helper::f($monthlyPageviews) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/revenue.png" alt="<?php echo Yii::t("website", "Monthly Ads Revenue") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Monthly Ads Revenue") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $monthlyAdsRevenue ? Helper::formatCurrencyPrice($monthlyAdsRevenue) : "n/a" ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-4">
					<table class="table custom-border">
						<thead>
							<tr>
								<th colspan="2">
									<?php echo Yii::t("website", "Estimated Yearly Stats") ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/visitors.png" alt="<?php echo Yii::t("website", "Yearly Unique Visitors") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Yearly Unique Visitors") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $yearlyVisitors ? Helper::f($yearlyVisitors) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/pageviews.png" alt="<?php echo Yii::t("website", "Yearly Pageviews") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Yearly Pageviews") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $yearlyPageviews ? Helper::f($yearlyPageviews) : "n/a" ?></td>
							</tr>
							<tr>
								<td style="vertical-align: middle">
									<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/revenue.png" alt="<?php echo Yii::t("website", "Yearly Ads Revenue") ?>">
									&nbsp;
									<?php echo Yii::t("website", "Yearly Ads Revenue") ?>
								</td>
								<td style="vertical-align: middle"><?php echo $yearlyAdsRevenue ? Helper::formatCurrencyPrice($yearlyAdsRevenue) : "n/a" ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
    </div>
</div>

<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/info.png" alt="<?php echo Yii::t("website", "Basic information") ?>">
        &nbsp;
        <?php echo Yii::t("website", "Basic information") ?>
    </div>
    <div class="card-body">
        <table class="table custom-border">
            <tr>
                <td class="width30pers">
                    <?php echo Yii::t("website", "Domain name") ?>
                </td>
                <td>
                    <strong><?php echo $website->idn ?></strong>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo Yii::t("website", "Title") ?>
                </td>
                <td>
                    <p style="word-break: break-all;"><?php echo CHtml::encode(html_entity_decode($website->meta_tags->title)) ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo Yii::t("website", "Keywords") ?>
                </td>
                <td>
                    <p style="word-break: break-all;"><?php echo CHtml::encode(html_entity_decode($website->meta_tags->keywords)) ?></p>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo Yii::t("website", "Description") ?>
                </td>
                <td>
                    <p style="word-break: break-all;"><?php echo CHtml::encode(html_entity_decode($website->meta_tags->description)) ?></p>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mb-20">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/search_engine.png" alt="<?php echo Yii::t("website", "Search Engine Stats") ?>">
                &nbsp;
                <?php echo Yii::t("website", "Search Engine Stats") ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table custom-border">
                            <tr>
                                <td class="vmiddle">
                                    <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/google.png" alt="<?php echo Yii::t("website", "Google Index") ?>">
                                    &nbsp;
                                    <?php echo Yii::t("website", "Google Index") ?>
                                </td>
                                <td class="vmiddle">
                                    <strong><?php echo Helper::f($website->search_engine->google_index) ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="vmiddle">
                                    <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/yahoo.png" alt="<?php echo Yii::t("website", "Yahoo Index") ?>">
                                    &nbsp;
                                    <?php echo Yii::t("website", "Yahoo Index") ?>
                                </td>
                                <td class="vmiddle">
                                    <strong><?php echo Helper::f($website->search_engine->yahoo_index) ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="vmiddle">
                                    <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/bing.png" alt="<?php echo Yii::t("website", "Bing Index") ?>">
                                    &nbsp;
                                    <?php echo Yii::t("website", "Bing Index") ?>
                                </td>
                                <td class="vmiddle">
                                    <strong><?php echo Helper::f($website->search_engine->bing_index) ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="vmiddle">
                                    <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/backlink.png" alt="<?php echo Yii::t("website", "Google Backlinks") ?>">
                                    &nbsp;
                                    <?php echo Yii::t("website", "Google Backlinks") ?>
                                </td>
                                <td class="vmiddle">
                                    <strong><?php echo Helper::f($website->search_engine->google_backlinks) ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/facebook.png" alt="<?php echo Yii::t("website", "Facebook Stats") ?>">
                &nbsp;
                <?php echo Yii::t("website", "Facebook Stats") ?>
            </div>
            <div class="card-body">
                <table class="table custom-border">
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Share count") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::f($website->social->facebook_share_count) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Comment count") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::f($website->social->facebook_comment_count) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Comment plugin count") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::f($website->social->facebook_comment_plugin_count) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Reaction count") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::f($website->social->facebook_reaction_count) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Total count") ?>
                        </td>
                        <td>
                            <strong><?php echo Helper::f($website->social->facebook_total_count) ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if($website->moz AND $moz->isEnabled()): ?>
<div class="card mb-20">
    <div class="card-header">
        <a href="http://moz.com" rel="nofollow" target="_blank"><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/moz.png" height="32px" alt="MOZ"></a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table custom-border">
                    <tr>
                        <td class="vmiddle width30pers">
                            <span><?php echo Yii::t("website", "Domain Authority") ?>
                            &nbsp;&nbsp;
                            <i class="fa fa-question-circle fa-2x fa-moz" aria-hidden="true" data-toggle="tooltip" data-placement="right" title='<?php echo CHtml::encode(Yii::t("website", "Domain Authority Description")) ?>'></i>
                            </span>
                        </td>
                        <td class="vmiddle">
                            <div class="progress moz">
                                <div class="progress-bar" style="width: <?php echo number_format($website->moz->pda, 2); ?>%;">
                                    <?php echo number_format($website->moz->pda, 2); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php /*
                    <tr>
                        <td class="vmiddle width30pers">
                            <?php echo Yii::t("website", "Page Authority") ?>
                            &nbsp;&nbsp;
                            <i class="fa fa-question-circle fa-2x fa-moz" aria-hidden="true" data-toggle="tooltip" data-placement="right" title='<?php echo CHtml::encode(Yii::t("website", "Page Authority Description")) ?>'></i>
                        </td>
                        <td class="vmiddle">
                            <div class="progress moz">
                                <div class="progress-bar" style="width: <?php echo number_format($website->moz->upa, 2); ?>%;">
                                    <?php echo number_format($website->moz->upa, 2); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="vmiddle width30pers">
                            <?php echo Yii::t("website", "MOZ Links") ?>
                            &nbsp;&nbsp;
                            <i class="fa fa-question-circle fa-2x fa-moz" aria-hidden="true" data-toggle="tooltip" data-placement="right" title='<?php echo CHtml::encode(Yii::t("website", "MOZ Links Description")) ?>'></i>
                        </td>
                        <td class="vmiddle">
                            <p><strong><?php echo Helper::f($website->moz->uid) ?></strong></p>
                        </td>
                    </tr>
                    <?php */ ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php /*
<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/alexa.png" alt="<?php echo Yii::t("website", "Alexa Stats") ?>">
        <?php echo Yii::t("website", "Alexa Stats") ?>
    </div>
    <div class="card-body">
        <?php $this->widget('application.widgets.AlexaStatsRenderer', array('alexa'=>$website->alexa)); ?>
    </div>
</div>
*/ ?>

<div class="row">
    <div class="col-md-6 mb-20">
        <div class="card">
            <div class="card-header">
                <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/antivirus.png" alt="<?php echo Yii::t("website", "Antivirus Stats") ?>">
                &nbsp;
                <?php echo Yii::t("website", "Antivirus Stats") ?>
            </div>
            <div class="card-body">
                <table class="table custom-border">
                    <tr>
                        <td class="width30pers vmiddle">
                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/google.png" alt="<?php echo Yii::t("website", "Google") ?>">
                            &nbsp;
                            <?php echo Yii::t("website", "Google") ?>
                        </td>
                        <td class="vmiddle">
                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/<?php echo $website->antivirus->google ?>.png" alt="<?php echo $website->antivirus->google ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="vmiddle">
                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/norton.png" alt="<?php echo Yii::t("website", "Norton") ?>">
                            &nbsp;
                            <?php echo Yii::t("website", "Norton") ?>
                        </td>
                        <td class="vmiddle">
                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/<?php echo $website->antivirus->avg ?>.png" alt="<?php echo $website->antivirus->avg ?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-20">
        <div class="card">
            <div class="card-header">
                <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/social.png" alt="<?php echo Yii::t("website", "Social Stats") ?>">
                &nbsp;
                <?php echo Yii::t("website", "Social Stats") ?>
            </div>
            <div class="card-body">
                <table class="table custom-border">
                    <tr>
                        <td class="width30pers vmiddle">
                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/pinterest.png" alt="<?php echo Yii::t("website", "Pins") ?>">
                            &nbsp;
                            <?php echo Yii::t("website", "Pins") ?>
                        </td>
                        <td class="vmiddle">
                            <strong><?php echo Helper::f($website->social->pins) ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/location.png" alt="<?php echo Yii::t("website", "Location Stats") ?>">
        &nbsp;
        <?php echo Yii::t("website", "Location Stats") ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table custom-border">
                    <tr>
                        <td class="width30pers">
                            <?php echo Yii::t("website", "IP Address") ?>
                        </td>
                        <td>
                            <strong><?php echo CHtml::encode($website->location->ip) ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="vmiddle">
                           <?php echo Yii::t("website", "Country") ?>
                        </td>
                        <td class="vmiddle">
                            <strong><?php echo $country->getCountryName($website->location->country_code, strtoupper($website->location->country_code)) ?></strong>
                            <?php echo Helper::getFlagUrl($website->location->country_code); ?>
                        </td>
                    </tr>
                    <?php if($website->location->region_name): ?>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "Region") ?>
                        </td>
                        <td>
                            <strong><?php echo CHtml::encode($website->location->region_name) ?></strong>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if($website->location->city): ?>
                    <tr>
                        <td>
                            <?php echo Yii::t("website", "City") ?>
                        </td>
                        <td>
                            <strong><?php echo CHtml::encode($website->location->city)?></strong>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if($website->location->longitude OR $website->location->latitude): ?>
                        <tr>
                            <td>
                                <?php echo Yii::t("website", "Longitude") ?>
                            </td>
                            <td>
                                <strong><?php echo CHtml::encode($website->location->longitude) ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Yii::t("website", "Latitude") ?>
                            </td>
                            <td>
                                <strong><?php echo CHtml::encode($website->location->latitude)?></strong>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
            <?php if(Yii::app()->params['google.browser_key']): ?>
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe
                            style="border:0"
                            loading="lazy"
                            allowfullscreen
                            src="https://www.google.com/maps/embed/v1/place?key=<?php echo Yii::app()->params['google.browser_key'] ?>&q=<?php echo $website->location->latitude ?>,<?php echo $website->location->longitude ?>&zoom=8">
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card mb-20">
    <div class="card-header">
        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/whois.png" alt="<?php echo Yii::t("website", "WHOIS") ?>">
        &nbsp;
        <?php echo Yii::t("website", "WHOIS") ?>
        <div class="pull-right">
            <a class="btn btn-secondary dropdown-toggle" id="dropdown-toggle" data-toggle="collapse" href="#collapseWhois">
            </a>
        </div>
    </div>
    <div class="panel-collapse collapse" id="collapseWhois">
        <div class="card-body">
            <?php echo str_replace("\n", "<br/>", CHtml::encode($website->whois->text)) ?>
        </div>
    </div>
</div>

<a id="widget"></a>
<div class="jumbotron">
	<h3 class="mb-20"><?php echo Yii::t("website", "Show Your Visitors Your Website Value") ?></h3>
	<div class="row">
		<div class="col-sm-6 col col-md-3  col-lg-3 mb-20">
			<?php echo $widget ?>
		</div>

		<div class="col-sm-6 col-md-9 col-lg-9">
			<div class="card mb-20">
					<div class="card-header">
                        <i class="fa fa-code fa-lg"></i>&nbsp;<?php echo Yii::t("website", "Get code") ?>
                        <div class="pull-right">
                                <a class="btn btn-secondary btn-xs dropdown-toggle" data-toggle="collapse" href="#collapseWidget">
                                </a>
                        </div>
					</div>
					<div class="panel-collapse collapse" id="collapseWidget">
							<div class="card-body">
									<textarea rows="3" class="form-control" onclick="this.focus();this.select()" readonly="readonly">
<?php echo trim(CHtml::encode($widget)) ?>
									</textarea>
							</div>
					</div>
			</div>
		</div>
	</div>
	<?php if(!$website->sale AND Yii::app()->params['app.allow_user_auth']): ?>
	<div class="row">
		<div class="col-lg-12">
			<p>
				<?php echo Yii::t("website", "Website owner? {Sale Website}!", array(
					"{Sale Website}"=>CHtml::link(Yii::t("website", "Sale Website"), Yii::app()->user->isGuest ? $this->createUrl("website/sell") : $this->createUrl("sale/add", array("id"=>$website->id))),
				)) ?>
			</p>
		</div>
	</div>
	<?php endif; ?>
</div>

<a id="estimate"></a>
<?php $this->widget('application.widgets.RequestFormWidget', array(
	'hSize'=>3
)) ?>
