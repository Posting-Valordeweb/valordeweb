<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/user/create") ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> <?php echo Yii::t("user", "Create User") ?>
    </a>
</div>
<br/><br/>


<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header"><?php echo Yii::t("user", "User role") ?></div>
            <div class="card-body">
                <ul>
                    <?php $roles = User::getRoleList(); unset($roles[User::ROLE_ROOT]); ?>
                    <?php foreach($roles as $id=>$role): ?>
                        <li>
                            <?php echo CHtml::encode($role) ?> : <?php echo $id ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header"><?php echo Yii::t("user", "User status") ?></div>
            <div class="card-body">
                <ul>
                    <?php foreach(User::getStatusList() as $id=>$status): ?>
                        <li>
                            <?php echo CHtml::encode($status) ?> : <?php echo $id ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="statusMsg"></div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $user -> search(),
	'filter' => $user,
	'cssFile' => '',
	'itemsCssClass' => 'table table-bordered table-striped table-hover',
	'enableSorting' => false,
	'summaryCssClass' => 'float-right summary',
	'rowCssClassExpression' => '$data->getStatusCSS()',
	'afterAjaxUpdate'=>'function(id, data){ attachTooltip(); }',
	'htmlOptions'=>array(
		'class'=>'table-responsive',
	),
	'columns' => array(
		array(
			'name' => 'id',
			'value' => '$data -> id',
			'htmlOptions' => array(
				'style'=>'min-width: 80px'
			),
			'filter' => CHtml::textField('User[id]', isset($_GET['User']['id']) ? $_GET['User']['id'] : '', array(
				'class'=>'form-control'
			)),
		),
		array(
			'name' => 'email',
			'value' => '$data->email',
			'filter' => CHtml::textField('User[email]', isset($_GET['User']['email']) ? $_GET['User']['email'] : '', array(
				'class'=>'form-control',
			))
		),
		array(
			'name' => 'username',
			'value' => '$data->username',
			'filter' => CHtml::textField('User[username]', isset($_GET['User']['username']) ? $_GET['User']['username'] : '', array(
				'class'=>'form-control',
			))
		),
		array(
			'name' => 'role',
			'value' => '$data->role',
			'htmlOptions' => array(
				'style'=>'min-width: 100px'
			),
			'filter' => CHtml::textField('User[role]', isset($_GET['User']['role']) ? $_GET['User']['role'] : '', array(
				'class'=>'form-control',
			))
		),
		array(
			'name' => 'status',
			'value' => '$data->getStatusMessage()',
			'htmlOptions' => array(
				'style'=>'min-width: 100px'
			),
			'filter' => CHtml::textField('User[status]', isset($_GET['User']['status']) ? $_GET['User']['status'] : '', array(
				'class'=>'form-control',
			))
		),
		array(
			'name' => 'ip',
			'value' => '$data->ip',
			'filter' => false,
		),
		array(
			'name' => 'created_at',
			'value' => 'Yii::app()->dateFormatter->formatDateTime($data->registered_at)',
			'filter' => false,
		),
		array(
			'class' => 'CButtonColumn',
			'htmlOptions' => array(
				'width'=>'60px'
			),
			'template'=>'{view} {update} {delete}',

			'viewButtonImageUrl' => false,
			'viewButtonLabel' =>'',
			'viewButtonOptions' => array(
				'class' => 'grid-button-inline fas fa-eye',
				'data-toggle'=>'tooltip',
				'title'=>Yii::t("user", "User info"),
			),

			'deleteButtonImageUrl' => false,
			'deleteButtonLabel' =>'',
			'deleteButtonOptions' => array(
				'class' => 'grid-button-inline fas fa-trash',
				'data-toggle'=>'tooltip',
				'title'=>Yii::t("user", "Delete user"),
			),
			'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").append(data); }',

			'updateButtonImageUrl' => false,
			'updateButtonLabel' => '',
			'updateButtonOptions' => array(
				'class' => 'grid-button-inline fas fa-edit',
				'data-toggle'=>'tooltip',
				'title'=>Yii::t("user", "Edit user data"),
			),
		),
	),
	'pagerCssClass' => 'float-right',
	'pager' => array(
        'class'=>'LinkPager',
		'cssFile' => false,
		'header' => '',
		'hiddenPageCssClass' => 'disabled',
		'selectedPageCssClass' => 'active',
		'htmlOptions' => array(
			'class' => 'pagination pagination-sm flex-wrap',
		)
	)
)); ?>