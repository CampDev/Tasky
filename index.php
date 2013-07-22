<?php
session_start();
require_once('functions.php');
require_once('tent-markdown.php');
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>

	<?php if(isset($_SESSION['entity'])) { ?>
	<div style="width: 100%; height: 50px; color: white; background: gray;">
		<div style="margin: auto; max-width: 1000px; padding: 10px;">Tasky<a href="new_post_page.php"><img src="img/createpost.png" style="float: right;"></a><a href="logout.php" style="float: right;">Logout</a></div>
	</div>
	<?php } ?>

		<div id="body_wrap">
			<?php
			if (!isset($_SESSION['entity']) OR isset($_GET['error'])) {
				if (isset($_GET['error'])) {
					echo "<h2 class='error'>Error: ".urldecode($_GET['error'])."</h2>";
				} ?>
				<h2 class="page_heading">Welcome to Tasky</h2>
				<h3>Tasky is a <b>Task Managment App</b> based on <a href="https://tent.io">Tent</a></h3>
				<p><form align="center" action="auth.php" method="get"> 
					<input type="url" name="entity" placeholder="https://cacauu.tent.is" /> 
					<input type="submit" />
				</form></p>
			<?php }
			else { ?>
			<?php 
				$entity = $_SESSION['entity'];
				$entity_sub = $_SESSION['entity_sub'];

				if (isset($_SESSION['loggedin']) AND $_SESSION['loggedin'] == true) {
					echo "<h2 class='loggedin'>Logged in successfully!</h2>";
					unset($_SESSION['loggedin']);
				}

				if (isset($_SESSION['new_list'])) {
					echo "<h2 class='loggedin'>Created list \" ".$_SESSION['new_list']."\"</h2>";
					unset($_SESSION['new_list']);
				}

				if (isset($_SESSION['new_task'])) {
					echo "<h2 class='loggedin'>Created new task \" ".$_SESSION['new_task']."\"</h2>";
					unset($_SESSION['new_task']);
				}

				if (isset($_SESSION['completed_task'])) {
					echo "<h2 class='loggedin'>Completed task \"".$_SESSION['completed_task']."\"</h2>";
					unset($_SESSION['completed_task']);
				}

				if (isset($_SESSION['updated'])) {
					echo "<h2 class='loggedin'>Updated task \"".$_SESSION['updated']."\"</h2>";
					unset($_SESSION['updated']);
				}

				if (isset($_SESSION['deleted'])) {
					echo "<h2 class='error'>Deleted successfully</h2>";
					unset($_SESSION['deleted']);
				}

				$nonce = uniqid('Tasky_', true);
				$mac_posts = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$init_lists = curl_init();
				curl_setopt($init_lists, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1');
				curl_setopt($init_lists, CURLOPT_HTTPGET, 1);
				curl_setopt($init_lists, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($init_lists, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_posts.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
				$lists = curl_exec($init_lists);
				curl_close($init_lists);
				$lists = json_decode($lists, true);
				?>

					<?php					
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<table style=' width: 100%; max-width: 1000px; margin: auto;'>";
					foreach ($posts['posts'] as $task) {
						$content = $task['content'];
						echo "<tr>";
						echo "<td><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];

						if ($content['notes'] != '' AND !is_null($content['notes'])) {
							echo "<br><i><div style='font-size: 11px;'>".Tent_Markdown($content['notes'])."</div></i></a></td>";
						}
						else {
							echo "</td>";
						}


						if (isset($content['duedate']) AND $content['duedate'] != '') {
							if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
								echo "<td style='color: cd0d00;'>Today</td>";
							}
							else {
								echo "<td>".date('d/M/Y', $content['duedate'])."</td>";
							}
						}
						else {
							echo "<td></td>";
						}

						echo "<td><div class='prio_".$content['priority']."'>".$content['priority']."</div></td>";

						if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
						echo "<td style='color: #219807;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/unchecked.png'></a></td>";	
						}
						elseif (isset($content['status']) AND $content['status'] == 'Done') {
						echo "<td style='color: #aaa;'><img src='img/checked.png'></td>";	
						}
						else {
							echo "<td></td>";
						}

						echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png'></a></td>";
						echo "</tr>";
					}
					echo "</table>";
					?>
				
				<hr>
				<h4 align="center">Your Lists:<?php foreach ($lists['posts'] as $list) {
					if(!is_null($list['content']['name'])) {
						echo " <a href='index.php?list=".$list['id']."'>".$list['content']['name']."</a> |";
					}
				} ?>
				</h4>
					<p align="center"><b>Create a new list: </b>
					<form align="center" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" />
						<input type="submit">
					</form>
					</p>

				
			<?php }
		?>
		</div>
		<hr>
		<footer><h4>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h4>
		<h4><a href="developer.php">Developer Resources</a></h4>
		</footer>
	</body>
</html>
