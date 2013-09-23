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
        <link rel="icon" type="image/ico" href="favicon.ico" />
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

			<div class="container main">

				<div class="sidebar">
                    <?php include('sidebar.php'); ?>
                </div>

				<div class='task-list'>

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

                    /* Welcome page */

					if ($posts['posts'] == array()) { ?>
					<div class="filters">Tasks</div>
						<h2>Welcome to Tasky</h2>
                        <div style="text-align: center;">
                        <p>Follow these three simple steps to get started with Tasky:</p>
						<p>1. <a href="list.php">Create a list.</a></p>
						<p>2. <a href="new_post_page.php">Start adding tasks.</a></p>
						<p>3. Click on "All lists" to filter by list.</p>
                        </div><?php }

                    /* Tasks from all lists */

					else { ?>
							<div class="filters">Tasks Due Tomorrow</div>
						<?php 
						$tomorrow = new DateTime('tomorrow');
						$tomorrow = date_timestamp_get($tomorrow);
						foreach ($posts['posts'] as $task) {
							$content = $task['content']; 
							if (date('d/M/Y', $content['duedate']) == date('d/M/Y', $tomorrow) AND $content['status'] == 'todo' OR date('d/M/Y', $content['duedate']) == date('d/M/Y', $tomorrow) AND $content['status'] == 'To Do') { 
								$didDisplay = true;
								?>
							<div id='single-task' class='<?php echo strtolower($content['status']); ?>'>


							<?php if (isset($content['status']) == 'todo') { ?>
								<a href='task_handler.php?type=complete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img class="priority" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a>
							<?php }
							elseif (isset($content['status']) AND strtolower($content['status']) == 'done') { ?>
								<a href='task_handler.php?type=uncomplete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img class="priority" src="img/checkbox_done.svg"></a>
							<?php }
							else {
								echo "";
							} ?>

							<div class='task-body'>
									<div class='title'>
								<a class='edit' href='edit.php?type=update&id=<?php echo $task['id']; ?>'><?php echo $content['title']; ?></div>

							<?php if ($content['notes'] != '' AND !is_null($content['notes'])) { ?>
								<i><div class='note'><?php echo Tent_Markdown($content['notes']); ?></div></i></a></div>
							<?php }
							else { ?>
								</div>
							<?php }

							if (isset($content['duedate']) AND $content['duedate'] != '') {
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) { ?>
									<div class='date'>Today</div>
								<?php }
								elseif ($content['duedate'] < time() and strtolower($content['status']) != 'done') { ?>
									<div class='date' style='color: red;'><?php echo date('d/M/Y', $content['duedate']); ?></div>
								<?php }
								else { ?>
									<div class='date'><?php echo date('d/M/Y', $content['duedate']); ?></div>
								<?php }
							}
							else {
								echo "";
							}                ?>
							<a href='task_handler.php?type=delete&id=<?php echo $task['id']; ?>'><img class='delete' src="img/delete.svg"></a>
						</div>
						<?php }
					}
					if (!isset($didDisplay)) { ?>
							<h2>Nothing due tomorrow!</h2>
							<h3><a href="new_post_page.php">Create a new task!</a></h3>
						<?php }
					} ?>
					</div>
				<div class='clear'></div><?php }

                    /* Tasks from chosen list */

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
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];?>
							<div id='single-task' class='<?php echo strtolower($content['status']); ?>'>

							<?php if (isset($content['status']) == 'todo') { ?>
								<a href='task_handler.php?type=complete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img class="priority" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a>
							<?php }
							elseif (isset($content['status']) == 'done') { ?>
								<a href='task_handler.php?type=uncomplete&id=<?php echo $task['id']; ?>&parent=<?php echo $task['version']['id']; ?>'><img class="priority" src="img/checkbox_done.svg"></a>

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
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) { ?>
									<div class='date'>Today</div>
								<?php }
								elseif ($content['duedate'] < time() and strtolower($content['status']) != 'done') { ?>
									<div class='date' style='color: red;'><?php echo date('d/M/Y', $content['duedate']); ?></div>
								<?php }
								else { ?>
									<div class='date'><?php echo date('d/M/Y', $content['duedate']); ?></div>
								<?php }
							}
							else {
								echo "";
							} ?>

							<a href='task_handler.php?type=delete&id=<?php echo $task['id']; ?>'><img class='delete' src="img/delete.svg"></a>

							<?php echo "</div>";
						}
						echo "</div>";
					}
					else { ?>
						<h2>No tasks in <?php echo $current_list['post']['content']['name']; ?>. <a href="new_post_page.php">Add one!</a></h2>
					<?php }
				}
				?>
                <div class='clear'></div>

		<?php include('footer.php') ?>

        </div>

	</body>
</html>
