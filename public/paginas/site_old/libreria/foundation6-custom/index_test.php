<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Foundation for Sites</title>
	<link rel="stylesheet" href="css/foundation.css">
	<link rel="stylesheet" href="css/app.css">
</head>

<body>
	<div class="row">
		<div class="large-24 columns">
			<h1>Welcome to Foundation</h1>
		</div>
	</div>


	<div class="row">
		<?php for($i=1;$i<=12;$i++):?>
		<div class="column large-2 end" style="border:1px solid red;">
			<div style="background-color:red;">
				C<?=$i;?>
			</div>
		</div>
		<?php endfor;?>
	</div>



	<script src="js/vendor/jquery.js"></script>
	<script src="js/vendor/what-input.js"></script>
	<script src="js/vendor/foundation.js"></script>
	<script src="js/app.js"></script>
</body>

</html>
