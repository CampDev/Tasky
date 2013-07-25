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

<?php include('header.php'); ?>

			<?php
			if (!isset($_SESSION['entity']) OR isset($_GET['error'])) {
				if (isset($_GET['error'])) {
					echo "<h2 class='error'>Error: ".urldecode($_GET['error'])."</h2>";
				} ?>

<div id="landing-banner" style="background: #3D6AA2; height: 180px; width: 100%; color: white; padding-top: 60px; text-align: center;">
<h2>Tasky is a free <b>Task Managment App</b> based on <a href="https://tent.io">Tent</a></h2>
				<p><form align="center" action="auth.php" method="get" style="margin: 35px;"> 
					<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
					<input type="submit" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; background: #ddd;" />
				</form></p>
</div>




<div id="features" style="background: none; border: 0px; height: auto;">
<div class="container">

<h2><b>Clear your mind and start being productive!</b></h2>
<div id="feature"><h3>1. Create tasks</h3>They can be anything! From dentist appointments to groceries, from serious business to feeding your kitten.</div>
<div id="feature"><h3>2. Organise them</h3>Each task is part of a list, which you can filter even further using priorities, deadlines and labels.</div>
<div id="feature"><h3>3. Productivity!</h3>Keep your tasks in sync across all devices. Tasky automatically syncs everything with your tent provider.</div>
<img src="img/tasks.png" style="text-align: center; margin-top: 30px;">
</div>
</div>



<div id="features">
<h2 style="color: white;"><b>What makes us special</b></h2>
<div class="container">
<div id="feature"><h3 style="color: white;">We are inherently social</h3>Your tent friends are already here. See what they should be doing!</div>
<div id="feature"><h3 style="color: white;">Tight integration</h3>Tasky integrates perfectly with other tent applications.</div>
<div id="feature"><h3 style="color: white;">Open source</h3>Help us out! We are on Github. :)</div>
</div>
</div>


			<?php }
			elseif (isset($_SESSION['entity']) AND !isset($_GET['list'])) { 
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

				?>



				<div class='container'><div class='task-list'>

<div class="filters">Priority / title / deadline / status</div>

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
				echo "<table>";
				foreach ($posts['posts'] as $task) {
					$content = $task['content'];
					echo "<tr>";

                    if (isset($content['priority'])) {
                    	echo "<td style='width: 10px;'><div class='prio_".$content['priority']."'></div></td>";
                    }
                  	else {
                  		echo "<td></td>";
                  	}

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

					if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
						echo "<td style='color: #219807;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/unchecked.png'></a></td>";	
					}
					elseif (isset($content['status']) AND $content['status'] == 'Done') {
						echo "<td style='color: #aaa;'><a href='task_handler.php?type=uncomplete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/checked.png'></a></td>";	
					}
					else {
						echo "<td></td>";
					}

					echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png'></a></td>";
					echo "</tr>";
				}
				echo "</table></div>";
				?>
				<div class="sidebar">

                <b>Views</b>
                <li>All tasks</li>
                <li>Due today</li>
                <li>Upcoming</li>
                <li>Calendar</li>
                <br>
                <b>Labels</b>
					<p align="center">
					<form align="center" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" placeholder="Add new list" />
						<input type="submit">
					</form>
					</p>
                </div>

				
			<?php }
			elseif (isset($_SESSION['entity']) and isset($_GET['list'])) {
				$entity = $_SESSION['entity'];
				$entity_sub = $_SESSION['entity_sub'];	

				$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.$_GET['list'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$init = curl_init();
				curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.$_GET['list']);
				curl_setopt($init, CURLOPT_HTTPGET, 1);
				curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
				$posts = curl_exec($init);
				curl_close($init);
				$posts = json_decode($posts, true);
				echo "<table>";
				foreach ($posts['posts'] as $task) {
					$content = $task['content'];
					echo "<tr>";

                    if (isset($content['priority'])) {
                    	echo "<td style='width: 10px;'><div class='prio_".$content['priority']."'></div></td>";
                    }
                  	else {
                  		echo "<td></td>";
                  	}

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

					if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
						echo "<td style='color: #219807;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/unchecked.png'></a></td>";	
					}
					elseif (isset($content['status']) AND $content['status'] == 'Done') {
						echo "<td style='color: #aaa;'><a href='task_handler.php?type=uncomplete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/checked.png'></a></td>";	
					}
					else {
						echo "<td></td>";
					}

					echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png'></a></td>";
					echo "</tr>";
				}
				echo "</table>";
			?>
			<div class="list-menu">
				<h4><b>Your Lists</b></h4><?php foreach ($lists['posts'] as $list) {
					if(!is_null($list['content']['name'])) {
						echo "<li> <a href='index.php?list=".$list['id']."'>".$list['content']['name']."</a> </li>";
					}
				} ?>
					<p>
					<form align="center" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" />
						<input type="submit">
					</form>
					</p>
                </div>
			<?php 
			}
			?>
        </div>
<?php include('footer.php') ?>
	</body>
</html>