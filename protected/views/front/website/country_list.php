<script type="text/javascript">
$(document).ready(function(){
	var pie_data = [];

	<?php $i=0; foreach($topCountries as $topCountry) : ?>
	pie_data[<?php echo $i ?>] = {
		label: '&nbsp;&nbsp;<?php echo addslashes($country->getCountryName($topCountry->country_code, strtoupper($topCountry->country_code)) . ' ('. Helper::proportion($sum, $topCountry->countryTotal). '%)') ?>',
		data: <?php echo $topCountry->countryTotal ?>,
	}
	<?php $i++; endforeach; ?>

    console.log(pie_data);

	drawFlot();
	window.onresize = function(event) {
		drawFlot();
	}
	function drawFlot() {
		$.plot("#country-pie", pie_data, {
			series: {
				pie: {
					show: true
				}
			}
		});
	}

});
</script>

<h1 class="mb-20"><?php echo CHtml::encode($this->title) ?></h1>


<div id="country-pie" class="country-pie pull-left"></div>

<div class="clearfix"></div>

<br/>

<?php $this->renderPartial("top_breadcrumbs") ?>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th><?php echo Yii::t("website", "Country") ?></th>
			<th><?php echo Yii::t("website", "Number of websites") ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($dataProvider->getData() as $data): $url = $this->createUrl("website/country", array("id"=>strtolower($data->country_code))) ?>
		<tr>
			<td class="cell-link" width="80%">
				<a href="<?php echo $url ?>">
					<?php echo $country->getCountryName($data->country_code, strtoupper($data->country_code)) ?>
				</a>
			</td>
			<td class="cell-link">
				<a href="<?php echo $url ?>">
					<?php echo $data->countryTotal ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="pull-right">
<?php $this -> widget('LinkPager', array(
	'pages' => $dataProvider->getPagination(),
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