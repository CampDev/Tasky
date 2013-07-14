<?php
session_start();
require_once('functions.php');
		if (!isset($_GET['code'])) { //If there's no code...
			$error = 'Couldn\'t register app, please try again';
			header('Location: index.php?error='.$error);
		}
		else { //If there is a code...
			$entity = $_SESSION['entity_old'];
			unset($_SESSION['entity_old']);
			$entity_sub = str_replace("http://", "", $entity);
			$entity_sub = str_replace("https://", "", $entity_sub);
			$entity_sub = substr($entity_sub, 0, strlen($entity_sub)-1);
			$_SESSION['entity_sub'] = $entity_sub; //Setting the sub entity (no http(s) and / at the end) as a Session variable
			$time = time();

			$oauth_url = $entity."oauth/authorization";
			$nonce = uniqid('Tasky_', true); //Generating the nonce TODO: Use a PHP library to do that more secure
			$mac = generate_mac('hawk.1.header', $time, $nonce, 'POST', '/oauth/authorization', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

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

                $_SESSION['access_token'] = $result['access_token'];
                $_SESSION['hawk_key'] = $result['hawk_key'];
				$_SESSION['entity'] = $entity;
				$_SESSION['loggedin'] = true;
				header('Location: index.php');
			}
		}
?>