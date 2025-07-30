<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/mark-as-read") ?>">
    <i class="fas fa-pencil-alt"></i><?php echo Yii::t("post", "Mark as Read") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/remove-from-star") ?>">
    <i class="far fa-star"></i><?php echo Yii::t("post", "Remove from Starred folder") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/mark-as-important") ?>">
    <i class="fas fa-bookmark"></i><?php echo Yii::t("post", "Mark as Important") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/move-to-trash") ?>">
    <i class="fas fa-trash"></i><?php echo Yii::t("post", "Move to Trash") ?>
</a>