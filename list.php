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
				<h2>List management</h2>
				<?php 
				if ($lists['posts'] == '' OR $lists['posts'] == array()) {
					echo "No lists, create one!"; ?>
				<?php
				}
				elseif (isset($posts['error'])) {
					echo "<h3 style='color: red;'>Error: ".$posts['error']."</h3>";
				}
				else { ?>
                <?php
					echo "<div style='line-height: 300%;'>";
					foreach ($lists['posts'] as $list) {
						$content = $list['content'];
						echo "<div>";
						if (!is_null($content['name'])) {
							echo "<div><a href='edit.php?list=".$list['id']."'>".$content['name']."</a></div>";
							if (!is_null($content['description'])) {
								echo "<div>".$content['description']."</div>";
							}
							else {
								echo "<div></div>";
							}
							echo "<div style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$list['id']."'>
<svg style='float: right; margin-top: -26px; margin-right: 10px;' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='16px' height='16px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <polygon style='fill: rgb(82, 82, 82);' id='x-mark-icon' points='438.393,374.595 319.757,255.977 438.378,137.348 374.595,73.607 255.995,192.225 137.375,73.622
73.607,137.352 192.246,255.983 73.622,374.625 137.352,438.393 256.002,319.734 374.652,438.378 '></polygon> </svg>
                            </a></div>";
						}

						
						echo "</div>";
					}
					}
				?> <form  style="clear: both; height: 30px; padding-top: 10px;" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" placeholder="Add new list" class="text" style="width: 60%; float: left;"/>
						<input type="submit" class="submit" value="Add list">
					</form></div>

					</div>

        	</div>
	</body>
</html>
