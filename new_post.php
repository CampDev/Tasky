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
				<p>Message:</p>
				<p><textarea name="message" class="message"></textarea> </p>
				<p><input type="submit"></p>
			</form>
		</div>
		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
		<?php
		?>
	</footer>
	</body>
</html>