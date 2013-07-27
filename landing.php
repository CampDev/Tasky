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
	<div id="landing-banner">
	<div class="padded-container">
		<h2>Clear your mind and become productive!</h2>
		<?php if (isset($_GET['error'])) {
			echo "<h2 class='error'>".urldecode($_GET['error'])."</h2>";
		}
		if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin-top: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" class="sign-in" value="Sign in with Tent" />
			</form></p>
		<?php } ?>
	</div>
	</div>

	<div id="features" class="padded-container">
		<h2>Tasky is a free <b>Task Managment</b> App...</h2>
		<img src="img/tasks.png" class="landing-image"><div style="clear: both;">
		<div id="feature"><h3>Create tasks</h3>They can be anything! From dentist appointments to groceries, from serious business to feeding your kitten.</div>
		<div id="feature"><h3>Organise them</h3>Each task is part of a list, which you can filter even further using priorities, deadlines and labels.</div>
		<div id="feature"><h3>Feel productive</h3>Keep your tasks in sync across all devices. Tasky automatically syncs everything with your tent provider.</div>
	</div></div>

	<div id="features" style="background: #27ae60;">
		<div class="padded-container">
		<h2 style="color: white;">... made for everyone with a <b>life</b>...</h2>
		<div id="feature"><h3 style="color: white;">Personal</h3>Keep a list of your groceries, roofs that need repairing and birthday presents to buy, and share them with your partner in life.</div>
		<div id="feature"><h3 style="color: white;">Projects</h3>Weddings, school projects, birthday parties or even holiday preparations; Tasky will help you out.</div>
		<div id="feature"><h3 style="color: white;">Business</h3>Stay organised by assigning tasks to collegues and have tasks assigned to you. Subtasks, comments and attachments will ensure high productivity.</div>
	</div>

	<div id="features"><h2 style="color: white;">... sporting tons of <b>features</b>...</h2></div>

	<div id="features"><h2 style="color: white;">... and powered by the social <b>tent</b> protocol.</h2></div>

	<div id="features"><h2 style="color: white;"><b>Give it a try!</b></h2>
		<?php if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin-top: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" class="sign-in" value="Sign in with Tent" />
			</form></p>
		<?php } ?>

	</div>
</div>