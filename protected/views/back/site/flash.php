<?php if($messages): ?>
<br/><br/>
<?php endif; ?>

<?php foreach($messages as $key => $message): ?>
<div class="alert alert-dismissible alert-<?php echo $key ?>">
<?php echo $message ?>
<button type="button" class="close" data-dismiss="alert">×</button>
</div>
<?php endforeach; ?>