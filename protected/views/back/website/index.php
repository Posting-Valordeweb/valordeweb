<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header"><?php echo Yii::t("website", "Is On Sale") ?></div>
            <div class="card-body">
                <ul>
                    <li>
                        <?php echo Yii::t("misc", "On sale") ?> : <?php echo 1 ?>
                    </li>
                    <li>
                        <?php echo Yii::t("misc", "Not on sale") ?> : <?php echo 0 ?>
                    </li>
                    <li>
                        <?php echo Yii::t("misc", "All") ?> : <?php echo Yii::t("website", "Leave empty input") ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div id="statusMsg"></div>

<div class="table-responsive">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $website->search(),
	'filter' => $website,
	'cssFile' => '',
	'itemsCssClass' => 'table table-bordered table-striped table-hover',
	'enableSorting' => false,
	'summaryCssClass' => 'float-right summary',
	'rowCssClassExpression' => '$data->sale ? "success" : null',
	'afterAjaxUpdate'=>'function(id, data){ attachTooltip(); }',
	'columns' => array(
		array(
			'name' => 'id',
			'value' => '$data -> id',
			'htmlOptions' => array(
				'width' => '10%'
			),
			'filter' => CHtml::textField('Website[id]', isset($_GET['Website']['id']) ? $_GET['Website']['id'] : '', array(
				'class'=>'form-control'
			)),
		),
		array(
			'name' => 'domain',
			'value' => '$data->idn',
			'filter' => CHtml::textField('Website[domain]', isset($_GET['Website']['domain']) ? $_GET['Website']['domain'] : '', array(
				'class'=>'form-control',
			))
		),

		array(
			'name' => 'price',
			'value' => 'Helper::p($data->price)',
            'type'=>'raw',
			'filter' => CHtml::textField('Website[price]', isset($_GET['Website']['price']) ? $_GET['Website']['price'] : '', array(
				'class'=>'form-control',
			))
		),

		array(
			'name' => 'sale_search',
			'value' => '$data->sale ? Yii::t("admin", "Yes") : Yii::t("admin", "No")',
			'filter' => CHtml::textField('Website[sale_search]', isset($_GET['Website']['sale_search']) ? $_GET['Website']['sale_search'] : '', array(
				'class'=>'form-control',
			))
		),

		array(
			'name' => 'added_at',
			'value' => 'Yii::app()->dateFormatter->formatDateTime($data->added_at, "long", "medium");',
			'filter'=>false,
		),

		array(
			'class' => 'CButtonColumn',
			'htmlOptions' => array(
				'width'=>'80px'
			),
			'template'=>'{view} {calculate} {removeFromSale} {delete}',
			'buttons'=>array(
				'calculate'=>array(
					'label'=>'<i class="fas fa-sync-alt"></i>',
					'url'=>'CHtml::normalizeUrl(array("admin/website/calculate", "id"=>$data->id))',
					'options'=>array(
						'title'=>Yii::t("website", "Re-Calculate estimate price"),
						'data-toggle'=>"tooltip",
					),
					'imageUrl'=>false,
				),
				'removeFromSale'=>array(
					'label'=>'<i class="fas fa-minus-circle"></i>',
					'url'=>'CHtml::normalizeUrl(array("admin/website/remove-from-sale", "id"=>$data->id))',
					'options'=>array(
						'title'=>Yii::t("website", "Remove website from sale"),
						'data-toggle'=>"tooltip",
					),
					'visible'=>'!empty($data->sale)',
				),
			),

			'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").append(data); }',

			'viewButtonImageUrl' => false,
			'viewButtonLabel' =>'<i class="fas fa-eye"></i>',
			'viewButtonUrl'=>'CHtml::normalizeUrl(array("website/show", "domain"=>$data->domain))',
			'viewButtonOptions' => array(
				'data-toggle'=>'tooltip',
				'title'=>Yii::t("website", "View website info"),
				'target'=>'_blank',
			),

			'deleteButtonImageUrl' => false,
			'deleteButtonLabel' =>'<i class="fas fa-trash"></i>',
			'deleteButtonOptions' => array(
				'data-toggle'=>'tooltip',
				'title'=>Yii::t("website", "Remove website"),
			),
		),
	),
	'pagerCssClass' => 'pull-right',
	'pager' => array(
        'class'=>'LinkPager',
		'cssFile' => false,
		'header' => '',
		'hiddenPageCssClass' => 'disabled',
		'selectedPageCssClass' => 'active',
        'firstPageCssClass'=>'page-item',
        'previousPageCssClass'=>'page-item',
        'internalPageCssClass'=>'page-item',
        'nextPageCssClass'=>'page-item',
        'lastPageCssClass'=>'page-item',
		'htmlOptions' => array(
			'class' => 'pagination pagination-sm flex-wrap',
		)
	)
)); ?>
</div>