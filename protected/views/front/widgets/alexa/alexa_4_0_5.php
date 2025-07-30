<div class="row">
    <div class="col-md-12">
        <?php echo $this->render("alexa/_alexa_common", array(
            "alexa"=>$alexa,
            "delta"=>$delta,
            "delta_direction"=>$delta_direction,
        )) ?>
    </div>
    <?php if(!empty($similar_sites)) : ?>
    <div class="col-md-6 mb-20">
        <h5><?php echo Yii::t("website", "Similar Websites") ?></h5>
        <ul class="list-group">
            <?php foreach($similar_sites as $similar_site): ?>
                <li class="list-group-item"><?php echo CHtml::encode($similar_site['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if(!empty($related_keywords)) : ?>
    <div class="col-md-6 mb-20">
        <h5><?php echo Yii::t("website", "Related Keywords") ?></h5>
        <ul class="list-group">
            <?php foreach($related_keywords as $related_keyword): ?>
                <li class="list-group-item"><?php echo CHtml::encode($related_keyword) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
