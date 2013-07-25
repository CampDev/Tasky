<?php
	session_start();
	require_once('functions.php');
	require_once('tent-markdown.php');
	if (!isset($_SESSION['entity'])) {
		$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
	}
	else {
		$nonce = uniqid('Tasky_', true);
		$entity = $_SESSION['entity'];
		$entity_sub = $_SESSION['entity_sub'];
		$nonce = uniqid('Tasky_', true);
		$mac_lists = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
		$init_lists = curl_init();
		curl_setopt($init_lists, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1');
		curl_setopt($init_lists, CURLOPT_HTTPGET, 1);
		curl_setopt($init_lists, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($init_lists, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_lists.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
		$lists = curl_exec($init_lists);
		curl_close($init_lists);
		$lists = json_decode($lists, true);
?>
<html>
	<head>
		<title>Lists - Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<?php include_once('header.php'); ?>
			<div class="container">
				<?php
				//Getting the list post
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

				//Getting tasks from the list
				$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$init = curl_init();
				curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list']);
				curl_setopt($init, CURLOPT_HTTPGET, 1);
				curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
				$posts = curl_exec($init);
				curl_close($init);
				$posts = json_decode($posts, true);
                ?>

			    <div class="sidebar">
                <b>Lists</b><hr>

				<?php 
					foreach ($lists['posts'] as $list) {
						echo "<p><a href='list.php?list=".$list['id']."'>".$list['content']['name']."</a></p>";
					}
				?>

					<p align="center">
					<form align="center" method="post" action="task_handler.php?type=list">
						<input type="text" name="list_name" placeholder="Add new list" class="text"/>
						<input type="submit" class="text">
					</form>
                    </p>
                </div>

				<div class='task-list'>
				<div class="filters"><span style="margin-top: -8px; font-weight: bold; color: #4069a7; font-size: 22px; float: left;"><?php echo $current_list['post']['content']['name']; ?></span>Priority / title / deadline / status</div>
				<?php 
				if ($posts['posts'] == '' OR $posts['posts'] == array()) {
					echo "No posts in ".$current_list['post']['content']['name']."";
				}
				elseif (isset($posts['error'])) {
					echo "<h3 style='color: red;'>Error: ".$posts['error']."</h3>";
				}
				else { ?>
                <?php
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
					}
				?>
        	</div>
	</body>
</html>
<?php } ?>