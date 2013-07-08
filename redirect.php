<?php
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
		if (!isset($_GET['code'])) { //If there's no code...
			echo "<p>No access, please try again!</p>"; //TODO: Add Error Message for easier debugging
		}
		else { //If there is a code...
			$entity = $_SESSION['entity_old'];
			unset($_SESSION['entity_old']);
			$entity_sub = substr($entity, 7, strlen($entity)-8);
			$time = time();

			//$_SESSION['oauth_code'] = $_GET['code'];
			$oauth_url = $entity."oauth/authorization";
			$nonce = uniqid('Tasky_', true); //Generating the nonce TODO: Use a PHP library to do that more secure
			$mac_data = "hawk.1.header\n".$time."\n".$nonce."\nPOST"."\n/oauth/authorization\n".$entity_sub."\n80"."\n\n\n".$_SESSION['client_id']."\n\n"; //Setting the data used in the Mac for the header request
			$mac_sha256 = hash_hmac('sha256', $mac_data, $_SESSION['hawk_key'], true); //Encrypting mac_data with sha256, using the Hawk Key as a secret
			$mac = base64_encode($mac_sha256); //Base64-Encoding the mac_data

			echo "<p><b>Header Data: </b>".$mac_data."</p>";
			echo "<p><b>SHA256: </b>".$mac_sha256."</p>";
			echo "<p><b>Mac: </b>".$mac."</p>";
			echo '<p><b>Authorization</b>: Hawk id="'.$_SESSION['hawk_id'].'", mac="'.$mac.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"</p>';

			//Building the post data
			$access_code_raw = array(
				'code' => $_GET['code'],
				'token_type' => 'https://tent.io/oauth/hawk-token',
			);
			$access_code = json_encode($access_code_raw); //Encoding the post data to JSON

			//CURL request to get the access_token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $oauth_url); //Setting the request url
			curl_setopt($ch, CURLOPT_POST, 1); //Setting the request method to POST
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['hawk_id'].'", mac="'.$mac.'", ts="'.$time.'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
			curl_setopt($ch, CURLOPT_POSTFIELDS, $access_code); //Setting the post data
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result_json = curl_exec($ch); //Getting and decoding (next line) the result
			$result = json_decode($result_json, true);
			if(curl_exec($ch) === false) //Handling errors
			{
    			echo '<b>Curl-Error</b>: ' . curl_error($ch); //Curl-Errors go here
			}
			elseif (isset($result['error'])) { //Auth-Errors go here
				echo "<p><b>Auth-Error: </b>".$result['error']."</p>";
			}
			else //No error goes here
			{
				echo "<b>Curl Result: </b>";
    			var_export($result);
    			$_SESSION['entity'] = $entity;
    			$_SESSION['access_token'] = $result['access_token'];
    			$_SESSION['hawk_key'] = $result['hawk_key'];
				echo "<p>Awesome, Tasky is authenticated and you can start using it!</p>";
				echo '<p><a href="index.php">Home</a></p>';
			}
		}
		?>
	</div>
	<footer><h3>Created by <a href="https://cacauu.tent.is">^Cacauu</a></h3>
		<h3><a href="developer.php">Developer Resources</a></h3>
	</footer>
	</body>
</html>