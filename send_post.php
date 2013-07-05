<?php
session_start();
if (!isset($_SESSION['entity'])) {
	$error = "You're not logged in!";
	header('Location: index.php?error='.urlencode($error));
}
require_once('functions.php');
?>
<html>
	<head>
		<title>New Post - Tasky</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<div id="body_wrap">
			<h2 class="page_heading">Tasky</h2>
			<?php
			if (isset($_POST['message'])) {
				$post_raw = array(
					'type' => 'https://tent.io/types/status/v0#', //Only supports status at the moment, TODO: Add Tasks and Lists
					'content' => array(
						'text' => $_POST['message']
					)
				);
				$post_data = json_encode($post_raw);

				echo $post_data;

				$time = time();
				$nonce = uniqid('Tasky_', true); //Generating the nonce TODO: Use a PHP library to do that more secure

				//Generating the MAC for the request
				$entity = $_SESSION['entity'];
				$entity_sub = substr($entity, 7, strlen($entity)-8);

				echo "<h2>Posting</h2>";
				$mac_send = generate_mac('hawk.1.header', $time, $nonce, 'POST', '/posts', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], true);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="https://tent.io/types/status/v0#"')); //Setting the HTTP header
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$send = curl_exec($ch);
				curl_close($ch);
				$send = json_decode($send, true);
				if (isset($send['error'])) {
					echo "<p><b>Auth-Error: </b>".$send['error']."</p>";
				}
				else {
					var_export($send);
				}

				echo "<hr />";
				echo "<h2>Reading</h2>";

				$mac_posts = generate_mac('hawk.1.header', $time, $nonce, 'GET', '/posts?types=https://tent.io/types/status/v0', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

				$init = curl_init();
				curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=https://tent.io/types/status/v0');
				curl_setopt($init, CURLOPT_HTTPGET, 1);
				curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($init, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_posts.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'" Content-Type: application/vnd.tent.post.v0+json; type="https://tent.io/types/status/v0#"')); //Setting the HTTP header
				$posts = curl_exec($init);
				curl_close($init);
				$posts = json_decode($posts, true);

				if (isset($posts['error'])) { //Auth-Errors go here
					echo "<p><b>Auth-Error: </b>".$posts['error']."</p>";
				}
				else {
					var_export($posts);
				}
			}
			?>
		</div>
		<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
		<?php
		?>
	</footer>
	</body>
</html>