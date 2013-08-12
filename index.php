<?php
session_start();
if (!isset($_SESSION['entity'])) {
	header('Location: landing.php');
}
require_once('functions.php');
require_once('tent-markdown.php');
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="jquery.leanModal.min.js"></script>
		<script>
			$(function() {
    			$('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });		
			});
		</script>
	</head>

	<body>

	<?php include('header.php'); ?>

			<div class="container">
				<div class="sidebar">

                	<b>Quick views</b><hr>
                	All tasks<br>
                    Inbox<br>
                	Due today<br>
                	Upcoming<br>
                	Calendar<br>
                	<br>
                	<b>People</b><hr>Sort tasks by those who are involved<br>
                	<br>
                	<b>Labels</b><hr>Sort tasks by labels<br></br>
                		</div>

				<div class='task-list'>

				<div class="filters">Priority / title / deadline / status
</div>

				<?php
				if (!isset($_GET['list'])) {
					unset($_SESSION['redirect_list']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<div>";
					foreach ($posts['posts'] as $task) {
						$content = $task['content'];
						echo "<div id='single-task'>";

                    	if (isset($content['priority'])) {
                    		echo "<div style='float: right;' class='prio_".$content['priority']."'></div>";
                    	}
                  		else {
                  			echo "";
                  		}
                        echo "<div id='single-task-inner' class='".$content['status']."'>";


						if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
							echo "<span style='color: #219807; float: left; margin-top: 12px; height: 28px;  margin-right: 20px;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/unchecked.png'></a></span>";	
						}
						elseif (isset($content['status']) AND $content['status'] == 'Done' OR $content['status'] == 'done') {
							echo "<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=uncomplete&id=".$task['id']."&parent=".$task['version']['id']."'><img src='img/checked.png'></a></span>";	
						}
						else {
							echo "";
						}


						echo "<div class='task-body'><div class='title'><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];
                        echo "</div>";

						if ($content['notes'] != '' AND !is_null($content['notes'])) {
							echo "<i><div class='note'>".Tent_Markdown($content['notes'])."</div></i></a></div>";
						}
						else {
							echo "</div>";
						}

						if (isset($content['duedate']) AND $content['duedate'] != '') {
							if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
								echo "<span style='color: cd0d00;'>Today</span>";
							}
							else {
								echo "<div class='date'>".date('d/M/Y', $content['duedate'])."</div>";
							}
						}
						else {
							echo "";
						}




						echo "<span style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'><img src='img/delete.png' class='delete' style='float: right; margin-top: -28px; margin-right: 10px;'></a></span>";

						echo "</div></div>";

					}

					echo "</div></div></div>";
				}
				elseif (isset($_GET['list'])) {
					$_SESSION['redirect_list'] = $_GET['list'];
					$id = $_GET['list'];
					$entity_sub_list = substr_replace($_SESSION['entity'] ,"",-1);
					$current_url = str_replace("{entity}", urlencode($entity_sub_list), $_SESSION['single_post_endpoint']);
					$current_url = str_replace("{post}", $id, $current_url);
					$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub_list)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch_current = curl_init();
					curl_setopt($ch_current, CURLOPT_URL, $current_url);
					curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
					$current_list = curl_exec($ch_current);
					curl_close($ch_current);
					$current_list = json_decode($current_list, true);

					//Getting tasks from the chosen list
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<table>";
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];
							echo "<tr class='".$content['status']."'>";

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
					}
					else {
						echo "<h2>No tasks in \"".$current_list['post']['content']['name']."\"</h2>";
					}
				}
				?>
        </div>
		<?php include('footer.php') ?>

	</body>
</html>
