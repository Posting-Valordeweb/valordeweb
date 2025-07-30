<h3><?php echo Yii::t("mail", "You got new message!", array(), null, $user->lang_id) ?></h3>
<p>
    <?php echo Yii::t("mail", "Dear {Name}", array(
        "{Name}"=>CHtml::encode(Helper::mb_ucfirst($name)),
    ), null, $user->lang_id) ?>,
</p>
<p>
<?php echo Yii::t("mail", "You've received new message from {Name}. Open following link to see a message: {messageLink}", array(
    "{Name}"=>"<strong>".CHtml::encode(Helper::mb_ucfirst($fromName))."</strong>",
    "{messageLink}"=>$messageLink,
), null, $user->lang_id); ?>
</p>