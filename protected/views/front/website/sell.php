<h1><?php echo Yii::t("sale_instruction", "Sell Websites - Sell Domains") ?></h1>

<p><?php echo Yii::t("sale_instruction", "Do you want to sell your website, sell your domain name?") ?></p>

<p>
<?php echo Yii::t("sale_instruction", "{Portal} allows people you to sell your website.", array(
	"{Portal}"=>"<strong>".$brandUrl."</strong>",
)) ?>
</p>

<p><?php echo Yii::t("sale_instruction", "To tell people that you may sell your website or sell domain") ?></p>

<ol>
<li>
	<?php echo Yii::t("sale_instruction", "Register/Login to {Portal}", array(
		"{Portal}"=> "<strong>".$brandUrl."</strong>",
	)) ?>
</li>
<li>
	<?php echo Yii::t("sale_instruction", "Check your website/domain on {Portal}", array(
		"{Portal}"=>"<strong>".$brandUrl."</strong>",
	)) ?>
</li>
<li>
	<?php echo Yii::t("sale_instruction", 'After analysis and calculation of your website price done, click "Sell my website/domain"', array(
		"{Portal}"=>"<strong>".$brandUrl."</strong>",
	)); ?>
</li>
<li>
	<?php echo Yii::t("sale_instruction", "Site Verification page opens", array(
		"{Portal}"=>"<strong>".$brandUrl."</strong>",
	)) ?>
</li>
<li>
	<?php echo Yii::t("sale_instruction", "Check your domain/website listed on {Buy Websites} page", array(
		"{Buy Websites}"=>CHtml::link(Yii::t("misc", "Buy Websites"), $this->createUrl("category/index")),
	)) ?>
</li>
<li>
	<?php echo Yii::t("sale_instruction", "And wait for people contact with you.") ?>
</li>
</ol>
<br/>
<a class="btn btn-primary" href="<?php echo $this->createUrl("category/index") ?>"><?php echo Yii::t("sale_instruction", "Check websites and domains on sale") ?></a>
<br/><br/>