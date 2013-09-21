<?php
	if(session_id() == '') {
    	session_start();
	}
	require_once('functions.php');
	require_once('tent-markdown.php');
	if (!isset($_SESSION['entity'])) {
		$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Lists - Tasky</title>
        <link rel="icon" type="image/ico" href="favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
		<?php include_once('header.php'); ?>
			<div class="container">



				<div id='new-task'>
				<h2>Settings</h2>
                <a href="logout.php">Logout</a>

					</div>

        	</div>
	</body>
</html>
