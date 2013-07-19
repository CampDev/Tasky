<?php
//Session Stuff #1
session_start();
require_once('functions.php');
	if (!isset($_GET['entity'])) {
		$error = 'Please enter an entity and try again';
		header('Location: index.php?error='.$error);
	}
	else {
		//Check if entity already ends with /, if not adds /
		if (substr(urldecode($_GET['entity']), -1) == '/') {
			$entity = urldecode($_GET['entity']);
		}
		else {
			$entity = urldecode($_GET['entity'])."/";
		}

		$_SESSION['entity_old'] = $entity;

        $meta = discover_link($entity, false); //Using discover_entity-function from discovery.php with entity from get and no debugging features
        $_SESSION['new_post_endpoint'] = $meta['post']['content']['servers'][0]['urls']['new_post'];
        $_SESSION['posts_feed_endpoint'] = $meta['post']['content']['servers'][0]['urls']['posts_feed'];
        $_SESSION['single_post_endpoint'] = $meta['post']['content']['servers'][0]['urls']['post'];

        //Creating App Information JSON
        $app_json = array(
			'type' => 'https://tent.io/types/app/v0#',
			'content' => array(
				'name' => 'Tasky',
				'url' => 'http://cacauu.de/tasky/',
				'types' => array(
					'write' => array('https://tent.io/types/status/v0', 'http://cacauu.de/tasky/task/v0.1', 'http://cacauu.de/tasky/list/v0.1'),
					),
				'redirect_uri' => 'http://localhost:8888/tent-tasks/redirect.php',
				),
			'permissions' => array('public' => false),
		);
		$app = json_encode($app_json);

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
		$header_link = str_replace("<", "", $header_parsed['Link']);
		$header_link = str_replace(">", "", $header_link);
		$header_link = str_replace('; rel="https://tent.io/rels/credentials"', "", $header_link);

		$_SESSION['client_id'] = $body['post']['id']; //Making the client id available to other instances of tasky using PHP Session
		$_SESSION['hawk_id'] = $body['post']['mentions'][0]['post'];

		//Next CURL request, GET this time to get the Access Token
		$token_init = curl_init();
		curl_setopt($token_init, CURLOPT_URL, $header_link);
		curl_setopt($token_init, CURLOPT_HTTPGET, true);
		curl_setopt($token_init, CURLOPT_RETURNTRANSFER, 1);
		$access_token_raw = curl_exec($token_init);
		$access_token_array = json_decode($access_token_raw, true);
		$_SESSION['hawk_key'] = $access_token_array['post']['content']['hawk_key'];
		curl_close($token_init);

		header('Location: '.$entity.'oauth?client_id='.$body['post']['id']);
	}
?>