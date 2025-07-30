<h1 class="mb-20"><?php echo CHtml::encode($this->title) ?></h1>
<script type="text/javascript">
	$(document).ready(function(){
		dynamicThumbnail({<?php echo $website->id ?>:<?php echo $thumbnail ?>});
	});
</script>


<div class="jumbotron">
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <img class="img-thumbnail img-responsive" id="thumb_<?php echo $website->id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/loader.gif" alt="<?php echo $website->idn ?>" />
        </div>

        <div class="col-sm-6 col-md-8">
            <table class="table custom-border">
                <tr>
                    <td>
                        <?php echo Yii::t("sale", "Domain/Website") ?>
                    </td>

                    <td>
                        <strong><?php echo $website->idn ?></strong>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?php echo Yii::t("website", "Estimate Price") ?>
                    </td>

                    <td>
                        <strong><?php echo Helper::p($website->price) ?></strong>
                    </td>
                </tr>
            </table>
            <div class="btn-group-vertical">
                <a class="btn btn-sm btn-primary" target="_blank" href="<?php echo $this->createUrl("website/show", array("domain"=>$website->domain)) ?>">
                    <i class="fas fa-dollar-sign"></i>&nbsp;
                    <?php echo Yii::t("website", "Estimate Price") ?>
                </a>
                <a class="btn btn-sm btn-secondary" target="_blank" href="http://<?php echo $website->domain ?>">
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

<h4 class="mb-20">1. <?php echo Yii::t("sale", 'Insert widget on your main page and click "verify" button. Feel free to modify HTML/CSS, but leave do follow link.') ?></h4>
<div class="row">
	<div class="col-sm-6 col col-md-2  col-lg-2" style="margin-bottom:21px">
		<?php echo $widget ?>
	</div>

	<div class="col-sm-6 col-md-10 col-lg-10">
		<div class="card">
				<div class="card-header">
                    <i class="fa fa-code fa-lg"></i>&nbsp;
                    <?php echo Yii::t("website", "Get code") ?>
                    <div class="pull-right">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="collapse" href="#collapseWidget">

                            </button>
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


<script type="text/javascript">
$(document).ready(function() {
	var button=$("#verify");
	var response=$("#response");
	var progress=$("#progress-bar");
	button.click(function(){
		button.hide();
		progress.show();
		response.hide();
		response.html('');
		$.get("<?php echo $this->createUrl("sale/verify", array("d"=>$website->domain))?>", {}, function(data) {
		    console.log(data);
			response.removeClass();
			response.addClass(data.class);
			response.html(data.message);
			response.show();
			progress.hide();
			button.show();
		}).fail(function() {
			progress.hide();
			response.hide();
			button.show();
		});
		return false;
	});

});
</script>
<div id="response" style="display: none"></div>

<button id="verify" class="btn btn-success mb-20"><?php echo Yii::t("sale", "Verify") ?></button>

<div class="clearfix"></div>

<div id="progress-bar" class="progress mb-20" style="display: none">
		<div class="progress-bar progress-bar-striped progress-bar-animated"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		</div>
</div>
<br/>
<?php $this->renderPartial("_form", array(
	"onSale"=>$onSale,
	"catList"=>$catList,
	"title"=>"2. ". Yii::t("sale", "Fill out the form"),
)) ?>