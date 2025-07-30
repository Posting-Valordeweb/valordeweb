<?php foreach($flashes as $key => $message): ?>
<div class="alert alert-dismissible alert-<?php echo $key ?>">
<?php echo $message ?>
<button type="button" class="close" data-dismiss="alert">×</button>
</div>
<?php endforeach; ?>