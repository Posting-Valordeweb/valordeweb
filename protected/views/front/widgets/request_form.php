<script type="text/javascript">

function papulateErrors (obj, errors) {
	for(var e in errors) {
		if(typeof(errors[e]) == 'object')
			papulateErrors(obj, errors[e])
		else
			obj.append(errors[e] + '<br/>');
	}
}

function request() {
	var data = $("#request-form").serializeArray(),
			button = $("#submit"),
			progress = $("#progress-bar"),
			errObj = $("#errors");

	data.push({
		"name":"redirect",
		"value":"<?php echo $this->redirect ?>"
	}, {
		"name":"instant",
		"value":<?php echo (int)$this->instant ?>
	});

	errObj.hide();
	progress.show();
	errObj.html('');
	button.attr("disabled", true);

	$.getJSON('<?php echo $this -> requestUrl ?>', data, function(response) {
		button.attr("disabled", false);
		// If response's type is string then all is ok, redirect to statistics
		if(typeof(response) === 'string') {
			document.location.href = response;
			return true;
		}
		// If it's object, then display errors
		papulateErrors(errObj, response);
		progress.hide();
		errObj.show();
		if(window.grecaptcha && (response.verifyCode === undefined)) {
            grecaptcha.reset();
        }
	}).fail(function(xhr, ajaxOptions, thrownError) {
		papulateErrors(errObj, {0:xhr.status + ': ' + xhr.responseText});
		errObj.show();
		progress.hide();
		button.attr("disabled", false);
	});
}

$(document).ready(function() {
	$("#submit").click(function() {
		request();
		return false;
	});

	$("#website-form input").keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			request();
			return false;
		}
	});
});
</script>


<div class="jumbotron">
    <h<?php echo $this->hSize ?> class="display-4"><?php echo Yii::app()->name ?></h<?php echo $this->hSize ?>>
    <p class="lead"><?php echo Yii::t("website", "Estimated website cost of any domain") ?></p>

    <form role="form" id="request-form" class="mb-20">
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <?php echo CHtml::activeTextField($form, 'domain', array(
                        'class' => 'form-control form-control-lg',
                        'placeholder'=>Yii::app()->params['site_cost.placeholder'],
                        'id'=>'domain_name',
                    )); ?>

                    <span class="input-group-append">
                        <button type="submit" id="submit" class="btn btn-primary">
                            <?php echo Yii::t("website", "Calculate") ?>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <?php if(Helper::isAllowedCaptcha()): ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
                    "siteKey"=>Yii::app()->params['recaptcha.public'],
                    'model'=>$form,
                    'attribute'=>'verifyCode',
                    "widgetOpts"=>array(),
                )); ?>
            </div>
        </div>
        <?php endif; ?>
    </form>

    <div class="row">
        <div class="col-md-6 padr-5">
            <div class="alert alert-danger mb-20" id="errors" style="display: none"></div>

            <div class="clearfix"></div>

            <div id="progress-bar" class="progress mb-20" style="display: none">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
            </div>

            <div class="clearfix"></div>
            <p>
                <?php echo Yii::t("website", "{NumOfWebsites} total website price calculated", array(
                    "{NumOfWebsites}"=>'<span class="badge badge-success">'.Helper::f($total).'</span>',
                )) ?>
            </p>

        </div>
    </div>

</div><!--End of Widget wrapper-->


