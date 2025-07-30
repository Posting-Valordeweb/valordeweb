<div class="btn-group">
    <a href="<?php echo $this->createUrl("admin/user/index") ?>" class="btn btn-primary">
        <i class="fa fa-users"></i> <?php echo Yii::t("user", "Manage Users") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/user/update", array("id"=>$user->id)) ?>" class="btn btn-success">
        <i class="fas fa-edit"></i> <?php echo Yii::t("user", "Edit user data") ?>
    </a>
    <a href="<?php echo $this->createUrl("admin/user/reset-password", array("id"=>$user->id)) ?>" class="btn btn-warning">
        <i class="fa fa-unlock-alt"></i> <?php echo Yii::t("user", "Reset password") ?>
    </a>
    <a href="<?php echo $this->createUrl("post/index", array("owner"=>$user->id)) ?>" target="_blank" class="btn btn-info">
        <i class="fa fa-inbox"></i> <?php echo Yii::t("user", "User Inbox") ?>
    </a>

    <?php if($onSale): ?>
    <a href="<?php echo $this->createUrl("sale/index", array("owner"=>$user->id)) ?>" target="_blank" class="btn btn-secondary">
        <i class="fa fa-link"></i> <?php echo Yii::t("user", "User has {CountOnSale} website(-s) on sale", array(
					"{CountOnSale}"=>'<span class="badge">'. $onSale .'</span>',
        )) ?>
    </a>
    <?php endif; ?>
</div>
<br/><br/>

<div class="table-responsive">
	<table class="table table-hover">
		<thead class="thead-light">
			<tr>
				<th><?php echo Yii::t("user", "Field") ?></th>
				<th><?php echo Yii::t("user", "Value") ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td><?php echo Yii::t("misc", "ID") ?></th></td>
				<td><?php echo $user->id ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Email") ?></th></td>
				<td><?php echo $user->email ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Username") ?></th></td>
				<td><?php echo $user->username ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "User role") ?></th></td>
				<td><?php echo $user->getRoleMessage() ?></td>
			</tr>

			<tr class="<?php echo $user->getStatusCSS(); ?>">
				<td><?php echo Yii::t("user", "User status") ?></th></td>
				<td><?php echo $user->getStatusMessage() ?></td>
			</tr>

			<tr<?php if(!$user->hasConfirmedEmail()) echo ' class="warning"'; ?>>
				<td><?php echo Yii::t("user", "Confirmed Email") ?></th></td>
				<td><?php echo $user->hasConfirmedEmail() ? Yii::t("admin", "Yes") : Yii::t("admin", "No"); ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Allow send messages") ?></th></td>
				<td><?php echo $user->canSendMessage() ? Yii::t("admin", "Yes") : Yii::t("admin", "No"); ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Registered IP") ?></td>
				<td><?php echo $user->ip ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Last login IP Address") ?></td>
				<td><?php echo $user->last_ip_login ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Registered at") ?></td>
				<td><?php echo $user->registered_at ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Modified at") ?></td>
				<td><?php echo $user->modified_at ?></td>
			</tr>

			<tr>
				<td><?php echo Yii::t("user", "Last login at") ?></td>
				<td><?php echo $user->last_login_at ?></td>
			</tr>
		</tbody>
	</table>
</div>