<ul class="nav nav-pills nav-justified">
	<li class="nav-item">
		<a class="nav-link<?php echo stripos($this->action->id, 'top') !== false ? ' active' : null; ?>" href="<?php echo $this->createUrl("website/toplist") ?>">
			<?php echo Yii::t("website", "TOP by Cost") ?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link<?php echo stripos($this->action->id, 'country') !== false ? ' active' : null; ?>" href="<?php echo $this->createUrl("website/countrylist") ?>">
			<?php echo Yii::t("website", "TOP by Countries") ?>
		</a>
	</li>
</ul>
<br/>