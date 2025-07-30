<h1 class="mb-20"><?php echo CHtml::encode($this->title) ?></h1>

<?php if(isset($afterHeader)): ?>
<?php echo $afterHeader; ?>
<?php endif; ?>

<div class="clearfix"></div>

<?php echo $widget ?>