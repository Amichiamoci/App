<?php 
use PhpMvc\Html;
?>
<html>
<head>
  <title>
    <?=Html::getTitle('Hello, world!')?>
</title>
</head>
<body>
  <?php Html::renderBody(); ?>
  <?php Html::render('footer'); ?>
</body>
</html>