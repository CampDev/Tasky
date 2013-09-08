<?php
session_start();
require_once('functions.php');
		if (!isset($_GET['code'])) { //If there's no code...
			$error = 'Couldn\'t register app, please try again';
			header('Location: index.php?error='.$error);
		}
		elseif (isset($_GET['code']) AND $_GET['state'] == $_SESSION['auth_state']) {
		//If there is a code and the state is equal to the one from the session
			unset($_SESSION['state']);
			$entity = $_SESSION['entity_old'];
			unset($_SESSION['entity_old']);
			$entity_sub = str_replace("http://", "", $entity);
			$entity_sub = str_replace("https://", "", $entity_sub);
			$entity_sub = substr($entity_sub, 0, strlen($entity_sub)-1);
			$_SESSION['entity_sub'] = $entity_sub; //Setting the sub entity (no http(s) and / at the end) as a Session variable
			$time = time();

			$oauth_url = $_SESSION['oauth_endpoint'];
			$nonce = uniqid('Tasky_', true); //Generating the nonce TODO: Use a PHP library to do that more secure
			$mac = generate_mac('hawk.1.header', $time, $nonce, 'POST', '/'.str_replace($entity, "", $_SESSION['oauth_token_endpoint']), $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

			//Building the post data
			$access_code_raw = array(
				'code' => $_GET['code'],
				'token_type' => 'https://tent.io/oauth/hawk-token',
			);
			$access_code = json_encode($access_code_raw); //Encoding the post data to JSON
			//cURL request to get the access_token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $_SESSION['oauth_token_endpoint']); //Setting the request url
			curl_setopt($ch, CURLOPT_POST, 1); //Setting the request method to POST
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['hawk_id'], $mac, $time, $nonce, $_SESSION['client_id']))); //Setting the HTTP header
			curl_setopt($ch, CURLOPT_POSTFIELDS, $access_code); //Setting the post data
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result_json = curl_exec($ch); //Getting and decoding (next line) the result
			$result = json_decode($result_json, true);
			if(curl_exec($ch) === false) //Handling errors
			{
    			echo '<b>Curl-Error</b>: ' . curl_error($ch); //Curl-Errors go here
			}
			elseif (isset($result['error'])) { //Auth-Errors go here ?>
				<p><b>Auth-Error: </b><?php echo $result['error']; ?></p>
				<p><a href="index.php">Try again</a></p>
				<?php session_destroy();
			}
			else //No error goes here
			{
				// Writing the credentials file
				$credentials_file = array('hawk_key' => $result['hawk_key'], 'access_token' => $result['access_token'], 'client_id' => $_SESSION['client_id']);
				$credentials_file = json_encode($credentials_file,JSON_UNESCAPED_UNICODE);
				$file = fopen('logins/'.urlencode($entity).'.json', 'w') or die("Error opening output file");
				fwrite($file, $credentials_file);
				fclose($file);

				// Setting SESSION variables
                $_SESSION['access_token'] = $result['access_token'];
                $_SESSION['hawk_key'] = $result['hawk_key'];
				$_SESSION['entity'] = $entity;
				$_SESSION['loggedin'] = true;

				// Redirecting to index.php
				header('Location: index.php');
			}
		}
		elseif ($_SESSION['auth_state'] != $_GET['state']) {
			// If the state from GET is not equal to the state from SESSION...
			$error = 'Corrupt state. Please try again!';
			header('Location: index.php?error='.$error);
		}
		else {
			// If everything is broken...
			$error = 'Something went wrong. Please try again!';
			header('Location: index.php?error='.$error);
		}
?>