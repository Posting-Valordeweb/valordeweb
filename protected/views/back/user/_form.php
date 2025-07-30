<form method="post">

    <?php echo CHtml::errorSummary($user, null, null, array(
        'class' => 'alert alert-danger',
    )); ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'email'); ?>

        <?php echo CHtml::activeTextField($user, 'email', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>

    <?php if($user->isNewRecord): ?>
    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'password'); ?>

        <?php echo CHtml::activePasswordField($user, 'password', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'password2'); ?>
        <?php echo CHtml::activePasswordField($user, 'password2', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'username'); ?>
        <?php echo CHtml::activeTextField($user, 'username', array(
            'class' => 'form-control',
            'required' => true,
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'lang_id'); ?>
        <?php echo CHtml::activeDropDownList($user, 'lang_id', $languages, array(
            'class' => 'form-control',
            'options' => array(
                '-' => array(
                    'disabled' => 'disabled',
                ),
                '' => array(
                    'readonly' => 'readonly',
                ),
            ),
        )); ?>
    </div>

    <?php if($user->isNewRecord OR !$user->isSuperUser()): ?>
    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'role'); ?>

        <?php echo CHtml::activeDropDownList($user, 'role', $roleList, array(
            'class' => 'form-control',
        )); ?>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <?php echo CHtml::activeLabel($user, 'status'); ?>

        <?php echo CHtml::activeDropDownList($user, 'status', User::getStatusList(), array(
            'class' => 'form-control',
        )); ?>
    </div>

    <div class="form-group">
        <?php echo CHtml::activeCheckBox($user, 'email_confirmed', array(
            'uncheckValue' => User::EMAIL_NOTCONFIRMED,
            'value' => User::EMAIL_CONFIRMED,
        )); ?>
        <label for="User_email_confirmed">
            <?php echo Yii::t("user", "Confirmed Email") ?>
        </label>

    </div>

    <div class="form-group">
        <?php echo CHtml::activeCheckBox($user, 'can_send_message', array(
            'uncheckValue' => User::DISALLOW_MESSAGE,
            'value' => User::ALLOW_MESSAGE
        )); ?>
        <label for="User_can_send_message">
            <?php echo Yii::t("user", "Allow send messages") ?>
        </label>
    </div>

    <button class="btn btn-primary" type="submit">
        <?php echo $user->isNewRecord ?  Yii::t("misc", "Create") : Yii::t("misc", "Update"); ?>
    </button>

</form>