<table border="0" width="100%" cellpadding="5">
<tbody>
<tr>
	<td width="400">
		<img width="400" height="300" src="<?php echo htmlspecialchars($thumbnail) ?>" alt="<?php echo $item->idn ?>"/>
	</td>
	<td valign="top">
		<h1><?php echo $item->idn ?></h1>
		<p>
			<?php echo Yii::t("website", "Has Estimated Worth of") ?>
			<strong><?php echo Helper::p($item->price) ?></strong>
		</p>
		<table width="100%" border="0">
			<tbody>
                <?php /*
				<tr>
                    <td width="32">
                        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/alexa.png" alt="<?php echo Yii::t("website", "Alexa Rank") ?>"/>
                    </td>
					<td width="20%">
						<?php echo Yii::t("website", "Alexa Rank") ?>
					</td>
					<td><strong><?php echo Helper::f($item->alexa->rank) ?></strong></td>
				</tr>
                */ ?>
				<tr>
                    <td width="32">
                        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/google.png" alt="<?php echo Yii::t("website", "Google Index") ?>"/>
                    </td>
					<td width="20%">
						<?php echo Yii::t("website", "Google Index") ?>
					</td>
					<td><strong><?php echo Helper::f($item->search_engine->google_index) ?></strong></td>
				</tr>
                <tr>
                    <td width="32">
                        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/facebook.png" alt="<?php echo Yii::t("website", "Facebook Stats") ?>"/>
                    </td>
                    <td width="20%">
                        Facebook
                    </td>
                    <td><strong><?php echo Helper::f($item->social->facebook_total_count) ?></strong></td>
                </tr>
				<tr>
                    <td width="32">
                        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/norton.png" width="32" height="32" alt="<?php echo Yii::t("website", "Norton") ?>"/>
                    </td>
					<td width="20%">
						<?php echo Yii::t("website", "Norton") ?>
					</td>
					<td>
                        <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/<?php echo $item->antivirus->avg ?>.png" alt="<?php echo $item->antivirus->avg ?>"/>
                    </td>
				</tr>
			</tbody>
		</table>
		<br/><br/>
		<a href="<?php echo $url ?>"><?php echo Yii::t("website", "Explore more") ?></a>
	</td>
</tr>
</tbody>
</table>