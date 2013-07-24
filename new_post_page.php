<?php
session_start();
require_once('functions.php');
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

	<?php } ?>

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
				<h2>Create a new task:</h2>
				<form align="center" action="task_handler.php?type=task" method="post">
					<p><b>Title:</b> <input type="text" name="title" placeholder="Your awesome task" /></p>

					<p>List: <select name="list">
						<?php
						foreach ($lists['posts'] as $list) {
							if(!is_null($list['content']['name'])) {
								echo "<option value='".$list['id']."'>".$list['content']['name']."</option>";
							}
						}
						?>
					</select>Priority:
					<select name="priority" size="1">
						<option value="0">Low</option>
						<option SELECTED value="1">Average</option>
						<option value="2">High</option>
						<option value="3">Urgent</option>
					</select></p>
					<p>Due: <input type="date" name="duedate"/></p>
					<p><textarea name="notes" placeholder="Add a description" class="message"></textarea> </p>
					<p>You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text</p>					<p><input type="submit"></p>
				</form>

				<hr>


		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
		</footer>
	</body>
</html>
