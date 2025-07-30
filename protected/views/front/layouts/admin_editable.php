<?php $this->beginContent('/'.$this->_end.'/layouts/main'); ?>
<div class="alert alert-warning">
	<?php echo $this->warningTitle ?>
</div>
<?php echo $content; ?>
<div class="alert alert-warning">
	<?php echo $this->warningTitle ?>
</div>
<?php $this->endContent('/'.$this->_end.'/layouts/main'); ?>