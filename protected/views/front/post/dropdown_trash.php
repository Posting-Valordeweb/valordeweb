<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/restore-from-trash") ?>">
    <i class="fas fa-trash-restore-alt"></i><?php echo Yii::t("post", "Restore from Trash folder") ?>
</a>

<a class="dropdown-item inbox-operation" href="<?php echo $this->createUrl("post/completely-remove") ?>">
    <i class="fas fa-trash"></i><?php echo Yii::t("post", "Completely remove") ?>
</a>