<?php
  $model =& ApplicationModel::getInstance();
  header('Content-type: text/html;charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Cestovní železniční zápisník</title>
<link href="raillog.css" type="text/css" rel="stylesheet" />
</head>
<body>

<table>
<?php
	$cars = $model->getStatisticsCars();
	usort($cars, array('StatCountTuple', 'compare'));
?>
<?php foreach ($cars as $car): ?>
<tr>
<td><?php echo($car->getObject()); ?></td>
<td><?php echo($car->getCount()); ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>