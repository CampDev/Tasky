<?php
	session_start();
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
	<div id="landing-banner" style="background: #3D6AA2; height: 180px; width: 100%; color: white; padding-top: 60px; text-align: center;">
		<h2>Clear your mind and become productive!</h2>
		<?php if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; background: #ddd;" />
			</form></p>
		<?php } ?>
	</div>

	<div id="features" class="container">
		<h2>Tasky is a free <b>Task Managment</b> App...</h2>
		<div style="width: 800px; margin: auto;"><img src="img/tasks.png" style="text-align: center; margin-top: 30px; box-shadow: 0px 0px 40px gray; border-radius: 5px; width: 500px;"></div>
		<div id="feature"><h3>Create tasks</h3>They can be anything! From dentist appointments to groceries, from serious business to feeding your kitten.</div>
		<div id="feature"><h3>Organise them</h3>Each task is part of a list, which you can filter even further using priorities, deadlines and labels.</div>
		<div id="feature"><h3>Feel productive</h3>Keep your tasks in sync across all devices. Tasky automatically syncs everything with your tent provider.</div>
	</div>

	<div id="features" style="background: #27ae60;">
		<h2 style="color: white;">... made for everyone with a <b>life</b>...</h2>
		<div class="container">
		<div id="feature"><h3 style="color: white;">Personal</h3>Keep a list of your groceries, roofs that need repairing and birthday presents to buy, and share them with your partner in life.</div>
		<div id="feature"><h3 style="color: white;">Projects</h3>Weddings, school projects, birthday parties or even holiday preparations; Tasky will help you out.</div>
		<div id="feature"><h3 style="color: white;">Business</h3>Stay organised by assigning tasks to collegues and have tasks assigned to you. Subtasks, comments and attachments will ensure high productivity.</div>
	</div>

	<div id="features"><h2 style="color: white;">... sporting tons of <b>features</b>...</h2></div>

	<div id="features"><h2 style="color: white;">... and powered by the social <b>tent</b> protocol.</h2></div>

	<div id="features"><h2 style="color: white;"><b>Give it a try!</b></h2>
		<?php if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; background: #ddd;" />
			</form></p>
		<?php } ?>

	</div>
</div>