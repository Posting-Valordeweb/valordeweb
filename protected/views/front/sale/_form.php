<?php if(!empty($title)): ?>
<h4 class="mb-20"><?php echo CHtml::encode($title) ?></h4>
<?php endif; ?>

<form method="post" class="mb-20">
    <?php echo CHtml::errorSummary($onSale, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'category_id', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="col-lg-9">
            <?php echo CHtml::activeDropDownList($onSale, 'category_id', array(""=>Yii::t("misc", "Please choose category"), "-"=>'--------------') + $catList, array(
                'class' => 'form-control',
                'options' => array(
                    '-' => array(
                        'disabled' => 'disabled',
                    ),
                    '' => array(
                        'readonly' => 'readonly',
                    ),
            ))); ?>
        </div>
    </div>

    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'price', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="input-group col-lg-9">
            <div class="input-group-prepend">
                <span class="input-group-text"><?php echo Yii::app()->params['site_cost.currency'] ?></span>
            </div>
            <?php echo CHtml::activeTextField($onSale, 'price', array(
                'class' => 'form-control',
            )); ?>
        </div>
    </div>


    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'monthly_visitors', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="col-lg-9">
            <?php echo CHtml::activeTextField($onSale, 'monthly_visitors', array(
                'class' => 'form-control',
            )); ?>
        </div>
    </div>


    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'monthly_revenue', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="input-group col-lg-9">
            <div class="input-group-prepend">
                <span class="input-group-text"><?php echo Yii::app()->params['site_cost.currency'] ?></span>
            </div>
            <?php echo CHtml::activeTextField($onSale, 'monthly_revenue', array(
                'class' => 'form-control',
            )); ?>
        </div>
    </div>

    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'monthly_views', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="col-lg-9">
            <?php echo CHtml::activeTextField($onSale, 'monthly_views', array(
                'class' => 'form-control',
            )); ?>
        </div>
    </div>

    <div class="form-group row">
        <?php echo CHtml::activeLabel($onSale, 'description', array('class' => 'col-lg-3 col-form-label')); ?>
        <div class="col-lg-9">
            <?php echo CHtml::activeTextArea($onSale, 'description', array(
                'class' => 'form-control',
                'rows' => '5',
            )); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo $onSale->isNewRecord ? Yii::t("sale", "Put up for sale") : Yii::t("misc", "Update"); ?></button>
</form>
