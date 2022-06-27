<?php $templates=WeblamasTemplate::get_subtemplates();?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<?php wp_head();?>
</head>
<body>
	<?php require(THEMEPATH.'template/header.html');?>
	<?php WeblamasTemplate::loadTemplate($templates);?>
	<?php require(THEMEPATH.'template/footer.html');?>
</body>
<?php wp_footer();?>
</html> 