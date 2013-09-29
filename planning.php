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
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/'.str_replace($_SESSION['entity'], "", $_SESSION['posts_feed_endpoint']).'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&limit=1000', $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&limit=1000');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);

                    /* Welcome page */

					if ($posts['posts'] == array()) { ?>
					<div class="filters"><div class="filter-inner">Tasks</div></div>
						<h2>Welcome to Tasky</h2>
                        <div style="text-align: center;">
                        <p>Follow these three simple steps to get started with Tasky:</p>
						<p>1. <a href="list.php">Create a list.</a></p>
						<p>2. <a href="new_post_page.php">Start adding tasks.</a></p>
						<p>3. Click on "All lists" to filter by list.</p>
                        </div><?php }

                    /* Tasks from all lists */

					else { ?>
						<?php 
						$tomorrow = new DateTime('tomorrow');
						$tomorrow = date_timestamp_get($tomorrow);
						foreach ($posts['posts'] as $task) {
							$content = $task['content']; 
							if (date('d/M/Y', $content['duedate']) == date('d/M/Y', $tomorrow) AND $content['status'] == 'todo' OR date('d/M/Y', $content['duedate']) == date('d/M/Y', $tomorrow) AND $content['status'] == 'To Do') { 
								$tomorrow_array[] = $task;
							}
							elseif (date('d/M/Y', $content['duedate']) == date('d/M/Y', time()) AND $content['status'] == 'todo' OR date('d/M/Y', $content['duedate']) == date('d/M/Y', time()) AND $content['status'] == 'To Do') {
								$today_array[] = $task;
							}
							elseif ($content['duedate'] > time()) { 
								$upcoming_array[] = $task;
							}
						}	
						} 
						}
						if (isset($today_array)) { ?>

					<div class="filters"><div class="filter-inner">Today</div></div>
							<? foreach ($today_array as $today_task) {
								$content = $today_task['content']; ?>
								<div id='single-task' class='<?php echo strtolower($content['status']); ?>'>


								<?php if (isset($content['status']) == 'todo') { ?>
									<a href='task_handler.php?type=complete&id=<?php echo $today_task['id']; ?>&parent=<?php echo $today_task['version']['id']; ?>'><img class="priority" title="To do - <?php echo $content['priority']; ?> priority" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a>
								<?php }
								elseif (isset($content['status']) AND strtolower($content['status']) == 'done') { ?>
									<a href='task_handler.php?type=uncomplete&id=<?php echo $today_task['id']; ?>&parent=<?php echo $today_task['version']['id']; ?>'><img class="priority" title="Done - <?php echo $content['priority']; ?> priority" src="img/checkbox_done.svg"></a>
								<?php }
								else {
									echo "";
								} ?>

								<div class='task-body'>
									<div class='title'>
								<a class='edit' href='edit.php?type=update&id=<?php echo $today_task['id']; ?>'><?php echo $content['title']; ?></div>

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
								}    
								 ?>
								<a href='task_handler.php?type=delete&id=<?php echo $today_task['id']; ?>'><img class='delete' src="img/delete.svg"></a>
							</div>
								<?php } } ?>	

						<?php if (isset($tomorrow_array)) { ?>

					<div class="filters"><div class="filter-inner">Tomorrow</div></div>
							<? foreach ($tomorrow_array as $tomorrow_task) {
								$content = $tomorrow_task['content']; ?>
								<div id='single-task' class='<?php echo strtolower($content['status']); ?>'>


								<?php if (isset($content['status']) == 'todo') { ?>
									<a href='task_handler.php?type=complete&id=<?php echo $tomorrow_task['id']; ?>&parent=<?php echo $tomorrow_task['version']['id']; ?>'><img class="priority" title="To do - <?php echo $content['priority']; ?> priority" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a>
								<?php }
								elseif (isset($content['status']) AND strtolower($content['status']) == 'done') { ?>
									<a href='task_handler.php?type=uncomplete&id=<?php echo $tomorrow_task['id']; ?>&parent=<?php echo $tomorrow_task['version']['id']; ?>'><img class="priority" title="Done - <?php echo $content['priority']; ?> priority" src="img/checkbox_done.svg"></a>
								<?php }
								else {
									echo "";
								} ?>

								<div class='task-body'>
									<div class='title'>
								<a class='edit' href='edit.php?type=update&id=<?php echo $tomorrow_task['id']; ?>'><?php echo $content['title']; ?></div>

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
								}    
								 ?>
								<a href='task_handler.php?type=delete&id=<?php echo $tomorrow_task['id']; ?>'><img class='delete' src="img/delete.svg"></a>
							</div>
								<?php } } ?>

						<?php if (isset($upcoming_array)) { ?>

					<div class="filters"><div class="filter-inner">Later</div></div>
							<?php foreach ($upcoming_array as $upcoming_task) {
								$content = $upcoming_task['content']; ?>
								<div id='single-task' class='<?php echo strtolower($content['status']); ?>'>


								<?php if (isset($content['status']) == 'todo') { ?>
									<a href='task_handler.php?type=complete&id=<?php echo $upcoming_task['id']; ?>&parent=<?php echo $upcoming_task['version']['id']; ?>'><img class="priority" title="To do - <?php echo $content['priority']; ?> priority" src="img/checkbox_<?php echo $content['priority']; ?>.svg" /></a>
								<?php }
								elseif (isset($content['status']) AND strtolower($content['status']) == 'done') { ?>
									<a href='task_handler.php?type=uncomplete&id=<?php echo $upcoming_task['id']; ?>&parent=<?php echo $upcoming_task['version']['id']; ?>'><img class="priority" title="Done - <?php echo $content['priority']; ?> priority" src="img/checkbox_done.svg"></a>
								<?php }
								else {
									echo "";
								} ?>

								<div class='task-body'>
									<div class='title'>
								<a class='edit' href='edit.php?type=update&id=<?php echo $upcoming_task['id']; ?>'><?php echo $content['title']; ?></div>

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
								}    
								 ?>
								<a href='task_handler.php?type=delete&id=<?php echo $upcoming_task['id']; ?>'><img class='delete' src="img/delete.svg"></a>
							</div>
								<?php } } ?>
						</div>
					<div class='clear'></div><?php 

					include('footer.php'); ?>

        </div>

	</body>
</html>
