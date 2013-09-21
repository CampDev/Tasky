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

        <script type="text/javascript">
        <!--
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        }
        //-->
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
					if (isset($_SESSION['redirect_list'])) {
						$_GET['list'] = $_SESSION['redirect_list'];
					}
					if (isset($_GET['list'])) {
						$url = $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity']).'+'.$_GET['list'].'&limit=1000';	
						$_SESSION['redirect_list'] = $_GET['list'];
					}
					elseif (isset($_GET['filter']) AND $_GET['filter'] == 'todo') {
						$url = $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1%23todo&limit=1000';
					}
					elseif (isset($_GET['filter']) AND $_GET['filter'] == 'done') {
						$url = $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1%23done&limit=1000';
					}
					else {
						$url = $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&limit=1000';
						unset($_SESSION['redirect_list']);
					}
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', str_replace($entity, "/", $url), $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $url);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
                    /* Welcome page */

					if (!isset($_GET['filter']) AND !isset($_GET['list']) AND $posts['posts'] == array()) { ?>
					<div class="filters">Tasks</div>
						<h2>Welcome to Tasky</h2>
                        <div style="text-align: center;">
                        <p>Follow these three simple steps to get started with Tasky:</p>
						<p>1. <a href="list.php">Create a list.</a></p>
						<p>2. <a href="new_post_page.php">Start adding tasks.</a></p>
						<p>3. Click on "All lists" to filter by list.</p>
                    </div><?php }

                    elseif (isset($_GET['list']) AND $posts['posts'] == array()) { ?>
                    	<h2>No tasks in <?php echo $current_list['post']['content']['name']; ?>. <a href="new_post_page.php">Add one!</a></h2>
                    <?php }
                    elseif (isset($_GET['filter']) AND $_GET['filter'] == 'todo' AND $posts['posts'] == array()) { ?>
                    	<div class="filters">Tasks - <a href="index.php?filter=todo">To Do</a> | <a href="index.php?filter=done">Done</a></div>
                    	<h2>Nothing to do! <a href="new_post_page.php">Add a new task</a></h2>
                    <?php }
                    elseif (isset($_GET['filter']) AND $_GET['filter'] == 'done' AND $posts['posts'] == array()) { ?>
                    	<div class="filters">Tasks - <a href="index.php?filter=todo">To Do</a> | <a href="index.php?filter=done">Done</a></div>
                    	<h2>No done tasks! <a href="index.php">Go and complete one!</a></h2>
                    <?php }

                    /* Tasks from all lists */

					else { ?>
							<div class="filters">Tasks - <a href="index.php?filter=todo">To Do</a> | <a href="index.php?filter=done">Done</a></div>
						<?php foreach ($posts['posts'] as $task) {
							$content = $task['content']; ?>
							<div id='single-task' class='<?php echo strtolower($content['status']); ?>' class="">


							<?php if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') { ?>
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
							<a onclinck="confirm_detele(<?php echo $task['id']; ?>)" href='task_handler.php?type=delete&id=<?php echo $task['id']; ?>'><img class='delete' src="img/delete.svg"></a>
						</div>
						<?php 
					} ?>
					</div>
				<div class='clear'></div><?php } ?>

		<?php include('footer.php') ?>

        </div>

	</body>
</html>
