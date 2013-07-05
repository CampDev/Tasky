<?php
session_start();
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>
		<div id="body_wrap">
			<h1 class="page_heading">Welcome to Tasky</h1>
			<h2>Tasky is a <b>Task Managment App</b> based on <a href="https://tent.io">Tent</a></h2>
			<?php
			if (!isset($_SESSION['entity']) OR isset($_GET['error'])) {
				if (isset($_GET['error'])) {
					echo "<h2 class='error'>Error: ".urldecode($_GET['error'])."</h2>";
				}?>
				<form align="center" action="auth.php" method="get"> 
					<p>Entity: <input type="url" name="entity" placeholder="https://cacauu.tent.is" /> 
					<input type="submit" /></p> 
				</form>
			<?}
			else { ?>
				<h2>You got the following options:</h2>
				<h3><a href="new_post.php">Write a new post</a></h3>
				<h3><a href="read_posts.php">Read your posts</a></h3>
				<h3><a href="logout.php">Logout</a></h3>
			<?}
		?>
		</div>

		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
		</footer>
	</body>
</html>