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
<html>
	<head>
		<title>Lists - Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
		<?php include_once('header.php'); ?>
			<div class="container">



				<div id='new-task'>
				<h2>List management</h2>

					<form align="center" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" placeholder="Add new list" class="text" style="width: 70%"/>
						<input type="submit" class="text" style="width: 20%;">
					</form>
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
					echo "<table>";
					foreach ($lists['posts'] as $list) {
						$content = $list['content'];
						echo "<tr>";
						if (!is_null($content['name'])) {
							echo "<td><a href='edit.php?list=".$list['id']."'>".$content['name']."</a></td>";
							if (!is_null($content['description'])) {
								echo "<td>".$content['description']."</td>";
							}
							else {
								echo "<td></td>";
							}
							echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$list['id']."'><img src='img/delete.png'></a></td>";
						}

						
						echo "</tr>";
					}
					echo "</table></div>";
					}
				?>
        	</div>
	</body>
</html>
