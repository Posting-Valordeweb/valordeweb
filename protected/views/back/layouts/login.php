<?php $this->beginContent('/'.$this->_end.'/layouts/main'); ?>
<div class="container">

<div class="row">
	<div class="col-xs-12 col-sm-12">
			<?php echo $this -> renderPartial("//". $this->_end. "/site/flash", array(
				"messages"=>Yii::app() -> user -> getFlashes(),
			)) ?>
	</div>
</div>

<?php echo $content ?>

</div>
<?php $this->endContent('/'.$this->_end.'/layouts/main'); ?>