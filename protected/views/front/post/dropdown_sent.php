<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/mark-as-star") ?>">
    <i class="fas fa-star"></i><?php echo Yii::t("post", "Mark as Starred") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/mark-as-important") ?>">
    <i class="fas fa-bookmark"></i><?php echo Yii::t("post", "Mark as Important") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/move-to-trash") ?>">
    <i class="fas fa-trash"></i><?php echo Yii::t("post", "Move to Trash") ?>
</a>