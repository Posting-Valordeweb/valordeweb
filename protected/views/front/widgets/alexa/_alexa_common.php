<table class="table custom-border">
    <tr>
        <td class="width30pers">
            <?php echo Yii::t("website", "Global Rank") ?>
        </td>
        <td>
            <strong><?php echo Helper::f($alexa->rank) ?></strong>
            <?php if(!empty($delta_direction) AND !empty($delta)): ?>
                <?php if($delta_direction === "up"): ?>
                    <span class="badge badge-success badge-alexa-delta">
                        <i class="fas fa-level-up-alt"></i>
                        <?php echo Helper::f((int) $delta); ?>
                    </span>
                <?php else: ?>
                    <span class="badge badge-danger badge-alexa-delta">
                        <i class="fas fa-level-down-alt"></i>
                        <?php echo Helper::f((int) $delta); ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo Yii::t("website", "Links in") ?>
        </td>
        <td>
            <strong><?php echo Helper::f($alexa->linksin) ?></strong>
        </td>
    </tr>
    <?php if($alexa->country_code OR $alexa->country_rank): ?>
        <tr>
            <td class="vmiddle width30pers">
                <?php echo Yii::t("website", "Local Rank") ?>
            </td>
            <td>
                <strong><?php echo Helper::f($alexa->country_rank) ?></strong>
            </td>
        </tr>
        <tr>
            <td class="vmiddle">
                <?php echo Yii::t("website", "Country") ?>
            </td>
            <td class="vmiddle">
                <?php echo ECountryList::getInstance(Yii::app()->language)->getCountryName($alexa->country_code, '--') ?>
                <?php echo Helper::getFlagUrl($alexa->country_code); ?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if($alexa->speed_time AND $alexa->pct) :?>
        <tr>
            <td>
                <?php echo Yii::t("website", "Load speed") ?>
            </td>
            <td>
                <?php echo Yii::t("website", "{Seconds} Seconds", array(
                    "{Seconds}"=>Helper::f($alexa->speed_time/1000, 3)
                )) ?> (<?php echo Helper::alexaSpeed($alexa->pct); ?>)
            </td>
        </tr>
    <?php endif; ?>
</table>