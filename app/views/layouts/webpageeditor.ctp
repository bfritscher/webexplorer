<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo "Info1ere"; ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->script('jquery-1.4.2.min');
		echo $scripts_for_layout;
		echo $this->Html->css('webpageeditor');
	?>
</head>
<body>
<div id="container">
<?php if($flash = $this->Session->flash()){
	echo $flash;
	$html->scriptStart();
?>
$(document).ready(function() {
	setTimeout(function() {
          $('#flashMessage').slideUp(500);
      }, 4000);
});
<?php
	echo $html->scriptEnd();
} ?>
<?php echo $this->Session->flash('auth'); ?>

<?php echo $content_for_layout; ?>
</div>
<?php echo $this->element('sql_dump'); ?>

</body>
</html>