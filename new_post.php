<?php
session_start();
if (!isset($_SESSION['entity'])) {
	$error = "You're not logged in!";
	header('Location: index.php?error='.urlencode($error));
}
?>
<html>
	<head>
		<title>New Post - Tasky</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<div id="body_wrap">
			<h2 class="page_heading">Tasky</h2>
			<form align="center" action="send_post.php" method="post">
				<p><b>Title:</b> <input type="text" name="title" placeholder="Your awesome task" /></p>
				<p>Priority: <select name="priority" size="1"><option value="0">Low</option><option SELECTED value="1">Average</option><option value="2">High</option><option value="3">Urgent</option></select></p>
				<p><b>List:</b> <select name="list"><option>To Do</option></select></legend></p>
				<p>Notes:</p>
				<p><textarea name="notes" class="message"></textarea> </p>
				<p><input type="submit"></p>
			</form>
			<p align="center">Note: Bold fields are required</p>
		</div>
		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
	</footer>
	</body>
</html>