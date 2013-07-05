<?php
//Session Stuff #1
session_start();
?>
<html>
	<head>
		<title>Tasky</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
		<div id="body_wrap">
			<h2 class="page_heading">Tasky</h2>
<?php
		//TODO: Add / to $entity if it doesn't end with /
		if (!substr(urldecode($_GET['entity']), strlen(urldecode($_GET['entity']))-1) == '/') {
			//TODO: Make this thing work and add a / to the entity if needed
			$entity = $_GET['entity'];
			echo "<p>Your entity doesn't end with \"/\", please add one and try again!</p>";
		}
		else {
			$entity = $_GET['entity'];
		}

		//Session stuff #2
		$_SESSION['entity'] = $entity;

        require_once('functions.php');
        $meta = discover_link($entity, true); //Using discover_entity-function from discovery.php with entity from get and no debugging features
        $_SESSION['new_post_endpoint'] = $meta['post']['content']['servers'][0]['urls']['new_post'];
        $_SESSION['posts_feed_endpoint'] = $meta['post']['content']['servers'][0]['urls']['posts_feed'];

        //Creating App Information JSON
        $app_json = array(
			'type' => 'https://tent.io/types/app/v0#',
			'content' => array(
				'name' => 'Tasky',
				'url' => 'http://cacauu.de/tasky/',
				'types' => array(
					'read' => array('https://tent.io/types/basic-profile/v0', 'https://tent.io/types/status/v0', 'https://tent.io/types/essay/v0', 'http://cacauu.de/tasky/task/v0.1', 'http://cacauu.de/tasky/list/v0.1'),
					'write' => array('https://tent.io/types/basic-profile/v0', 'https://tent.io/types/status/v0', 'https://tent.io/types/essay/v0', 'http://cacauu.de/tasky/task/v0.1', 'http://cacauu.de/tasky/list/v0.1'),
					),
				'redirect_uri' => 'http://localhost:8888/tent-tasks/redirect.php',
				),
			'permissions' => array('public' => false),
		);
		$app = json_encode($app_json);
		var_export($app);
		echo "<hr />";

		//Doing the curl app registration
		$init = curl_init();
		curl_setopt($init, CURLOPT_URL, $meta['post']['content']['servers'][0]['urls']['new_post']); //Setting the URL
		curl_setopt($init, CURLOPT_POST, 1); //Setting request method to POST
		curl_setopt($init, CURLOPT_HEADER, true);
		curl_setopt($init, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.tent.post.v0+json; type="https://tent.io/types/app/v0#"', 'Content-Length: '.strlen($app)));
		curl_setopt($init, CURLOPT_POSTFIELDS, $app); //Setting the content of the post request
		curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($init);
		$header_size = curl_getinfo($init, CURLINFO_HEADER_SIZE);
		curl_close($init); //End of CURL request, result is saved in $result

		$header = substr($result, 0, $header_size);
		$body_json = substr($result, $header_size);
		$body = json_decode($body_json, true);

		$header_parsed = http_parse_headers($header);
		echo "<b>Parsed Header: </b>";
		var_export($header_parsed);
		$header_link = str_replace("<", "", $header_parsed['Link']);
		$header_link = str_replace(">", "", $header_link);
		$header_link = str_replace('; rel="https://tent.io/rels/credentials"', "", $header_link);
		echo "<p><b>Link: </b>".$header_link."</p>";

		//Exporting the variables (debugging-only)
		echo "<hr /><b>Response Header: </b>";
		var_export($header);
		echo "<hr /><b>Response Body: </b>";
		var_export($body);
		echo "<p><b>Client ID: </b>".$body['post']['id']."</p>";
		$_SESSION['client_id'] = $body['post']['id']; //Making the client id available to other instances of tasky using PHP Session
		echo "<p><b>Hawk ID: </b>".$body['post']['mentions'][0]['post']."</p>";
		$_SESSION['hawk_id'] = $body['post']['mentions'][0]['post'];
		echo "<hr />";

		//Next CURL request, GET this time to get the Access Token
		$token_init = curl_init();
		curl_setopt($token_init, CURLOPT_URL, $header_link);
		curl_setopt($token_init, CURLOPT_HTTPGET, true);
		curl_setopt($token_init, CURLOPT_RETURNTRANSFER, 1);
		$access_token_raw = curl_exec($token_init);
		$access_token_array = json_decode($access_token_raw, true);
		var_export($access_token_array);
		echo "<p><b>Hawk Key:</b> ".$access_token_array['post']['content']['hawk_key']."</p>";
		$_SESSION['hawk_key'] = $access_token_array['post']['content']['hawk_key'];
		curl_close($token_init);

		echo "<hr />";
		echo "<p><a href='".$entity."oauth?client_id=".$body['post']['id']."'>Authorize Tasky</a></p>";
?>
	</div>
	<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
	</footer>
	</body>
</html>