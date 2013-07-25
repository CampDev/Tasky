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
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>

	<body>
		<div id="body_wrap">
			<h2 class="page_heading">Tasky</h2>
			<?php
			if (isset($_GET['type'])) {
					switch ($_GET['type']) {
						case 'status': 
						//$type = 
						break;
		
						case 'essay': 
						//Create an essay
						break;
		
						case 'list':
						if (isset($_GET['description'])) {
							$description = $_GET['description'];
						}
						else {
							$description = '';
						}
						$type = 'http://cacauu.de/tasky/list/v0.1';
						$post_raw = array(
							'type' => 'http://cacauu.de/tasky/list/v0.1#',
							'permissions' => array(
								'public' => false,
							),
							'content' => array(
								'name' => $_GET['title'],
								'description' => $description,
							)
						);
						break;  
		
						case 'delete': 
						//Delete a post
						break;
		
						default:
						//Create a task
					}
				}
			elseif (isset($_POST['title']) && isset($_POST['priority'])) {
				$type = 'http://cacauu.de/tasky/task/v0.1';
				$post_raw = array(
					'type' => 'http://cacauu.de/tasky/task/v0.1#todo',
					'permissions' => array(
						'public' => false,
					),
					'content' => array(
						'title' => $_POST['title'],
						'priority' => $_POST['priority'],
						'note' => $_POST['notes'],
						'list' => $_POST['list'],
						'status' => 'To Do',
					),
					'mentions' => array(
						array(
							'post' => $_POST['list'],
						),
					),
				);
			}
			echo "<p><b>Type: </b>".$type."</p>";
			$post_data = json_encode($post_raw);

			echo $post_data;

			$time = time();
			$nonce = uniqid('Tasky_', true); //Generating the nonce TODO: Use a PHP library to do that more secure

			//Generating the MAC for the request
			$entity = $_SESSION['entity'];
			$entity_sub = $_SESSION['entity_sub'];

			echo "<p><b>Sub: </b>".$entity_sub."</p>";
			echo "<h2>Posting</h2>";
			$mac_send = generate_mac('hawk.1.header', $time, $nonce, 'POST', '/posts', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="'.$type.'#todo"')); //Setting the HTTP header
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

			$mac_posts = generate_mac('hawk.1.header', $time, $nonce, 'GET', '/posts?types='.$type, $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

			$init = curl_init();
			curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types='.$type);
			curl_setopt($init, CURLOPT_HTTPGET, 1);
			curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($init, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_posts.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'" Content-Type: application/vnd.tent.post.v0+json; type="'.$type.'"')); //Setting the HTTP header
			$posts = curl_exec($init);
			curl_close($init);
			$posts = json_decode($posts, true);

			if (isset($posts['error'])) { //Auth-Errors go here
				echo "<p><b>Auth-Error: </b>".$posts['error']."</p>";
			}
			else {
				var_export($posts['posts']);
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