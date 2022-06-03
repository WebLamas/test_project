<?php $templates=WeblamasTemplate::get_subtemplates();?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<?php wp_head();?>
</head>
<body>
	<?php WeblamasTemplate::loadTemplate($templates);?>
</body>
<?php wp_footer();?>
</html> 