<?php
session_start();
if (!isset($_SESSION['entity'])) {
	header('Location: landing.php');
}
require_once('functions.php');
require_once('tent-markdown.php');
?>
<!DOCTYPE html>
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

			<div class="container" style="background: rgb(245, 245, 245); border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
				<div class="sidebar">
                	All tasks<br><br>
                    Inbox<br><br>
                	Due today<br><br>
                	Upcoming<br><br>
                	Calendar
                		</div>

				<div class='task-list'>

				<div class="filters" style="text-align: left; font-size: 18px;">Tasks</div>

				<?php
				if (!isset($_GET['list'])) {
					unset($_SESSION['redirect_list']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/'.str_replace($_SESSION['entity'], "", $_SESSION['posts_feed_endpoint']).'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1', $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<div>";
					if ($posts['posts'] == array()) { ?>
						<h2>Welcome to Tasky</h2>
                        <div style="text-align: center;">
                        <p>Follow these three simple steps to get started with Tasky:</p>
						<p>1. <a href="list.php">Create a list.</a></p>
						<p>2. <a href="new_post_page.php">Start adding tasks.</a></p>
						<p>3. Click on "All lists" to filter by list.</p>
                        </div>
					<?php }
					else {
						foreach ($posts['posts'] as $task) {
							$content = $task['content']; ?>
							<div id='single-task'>
                        	<div id='single-task-inner' class='<?php echo strtolower($content['status']); ?>'>


							<?php if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') { ?>
								<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=complete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img width="25px" height="25px" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a></span>	
							<?php }
							elseif (isset($content['status']) AND strtolower($content['status']) == 'done') { ?>
								<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=uncomplete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img width="25px" height="25px" src="img/checkbox_done.svg"></a></span>
							<?php }
							else {
								echo "";
							} ?>

							<div class='task-body'>
								<div class='title'><a class='edit' href='edit.php?type=update&id=<?php echo $task['id']; ?>'><?php echo $content['title']; ?></div>

							<?php if ($content['notes'] != '' AND !is_null($content['notes'])) { ?>
								<i><div class='note'><?php echo Tent_Markdown($content['notes']); ?></div></i></a></div>
							<?php }
							else { ?>
								</div>
							<?php }

							if (isset($content['duedate']) AND $content['duedate'] != '') {
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
									echo "<div class='date'>Today</div>";
								}
								else {
									echo "<div class='date'>".date('d/M/Y', $content['duedate'])."</div>";
								}
							}
							else {
								echo "";
							}                ?>
							<span><a class='delete' href='task_handler.php?type=delete&id=<?php echo $task['id']; ?>'><img width="20px" height="20px" src="img/delete.svg"></a></span>
							</div>
						</div>
						<?php }
					} ?>
					</div>
				</div>
				<div style='clear: both;'></div>
				<?php }
				elseif (isset($_GET['list'])) {
					$_SESSION['redirect_list'] = $_GET['list'];
					$id = $_GET['list'];
					$entity_sub_list = substr_replace($_SESSION['entity'] ,"",-1);
					$current_url = str_replace("{entity}", urlencode($entity_sub_list), $_SESSION['single_post_endpoint']);
					$current_url = str_replace("{post}", $id, $current_url);
					$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', str_replace($_SESSION['entity'], "/", $current_url), $_SESSION['entity_sub'], '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch_current = curl_init();
					curl_setopt($ch_current, CURLOPT_URL, $current_url);
					curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
					$current_list = curl_exec($ch_current);
					curl_close($ch_current);
					$current_list = json_decode($current_list, true);

					//Getting tasks from the chosen list
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', str_replace($_SESSION['entity'], "/", $_SESSION['posts_feed_endpoint']).'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity']).'+'.$_GET['list'], $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity']).'+'.$_GET['list']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<div>";
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];?>
							<div id='single-task'>
                        	<div id='single-task-inner' class='<?php echo strtolower($content['status']); ?>'>

                    		<?php if (isset($content['priority'])) {
                    			echo "<div style='width: 10px;'><div class='prio_".$content['priority']."'></div></div>";
                    		}
                  			else {
                  				echo "<div></div>";
							} ?>

							<?php if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') { ?>
								<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=complete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img width="25px" height="25px" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a></span>
							<?php }
							elseif (isset($content['status']) AND $content['status'] == 'Done' OR $content['status'] == 'done') { ?>
								<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=uncomplete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img width="25px" height="25px" src="img/checkbox_done.svg"></a></span>

							<?php }
							else {
								echo "";
							} ?>

							<div class='task-body'>
								<div class='title'><a class='edit' href='edit.php?type=update&id=<?php echo $task['id']; ?>'><?php echo $content['title']; ?></div>

							<?php if ($content['notes'] != '' AND !is_null($content['notes'])) { ?>
								<i><div class='note'><?php echo Tent_Markdown($content['notes']); ?></div></i></a></div>
							<?php }
							else { ?>
								</div>
							<?php }

							if (isset($content['duedate']) AND $content['duedate'] != '') {
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
									echo "<div class='date'>Today</div>";
								}
								else {
									echo "<div class='date'>".date('d/M/Y', $content['duedate'])."</div>";
								}
							}
							else {
								echo "";
							}                ?>



							<span><a class='delete' href='task_handler.php?type=delete&id=<?php echo $task['id']; ?>'><img width="20px" height="20px" src="img/delete.svg"></a></span>
							<?php echo "</tr></div></div>";
						}
						echo "</div>";
					}
					else { ?>
						<h2>No tasks in <?php echo $current_list['post']['content']['name']; ?>. <a href="new_post_page.php">Add one!</h2>
					<?php }
				}
				?>
                </div>
                <div style='clear: both;'></div>
		<?php include('footer.php') ?>
        </div>

	</body>
</html>
