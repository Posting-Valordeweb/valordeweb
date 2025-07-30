<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php echo Yii::t("misc", "Language") ?>
    </a>
    <div class="dropdown-menu">
        <?php foreach($languages as $language):?>
            <?php if($language->id == Yii::app() -> language) continue;
            $url=Yii::app()->controller->createAbsoluteUrl('', array_merge($_GET, array("language"=>$language->id)));
            ?>
            <?php Yii::app() -> clientScript -> registerLinkTag(
                'alternate', null, $url, null, array(
                'hreflang' => $language->id,
            )); ?>
            <?php echo CHtml::link(Language::formatLanguage($language), $url, array(
                "class"=>"dropdown-item"
            )) ?>
        <?php endforeach; ?>
        <div class="dropdown-divider"></div>
        <a href="<?php echo Yii::app()->request->url ?>" class="dropdown-item disabled"><?php echo Language::formatLanguage($languages[Yii::app() -> language]) ?></a>
    </div>
</li>