<?php

use Librejo\App\App;
use Librejo\Client;

function register_app($entity) {
	if (file_exists('logins/'.urlencode($entity).'.json')) {
		$credentials = json_decode(file_get_contents('logins/'.urlencode($entity).'.json'), true);
		$_SESSION['entity_old'] = $entity;
		$_SESSION['hawk_key'] = $credentials['hawk_key'];
		$_SESSION['hawk_id'] = $credentials['access_token'];
		$_SESSION['client_id'] = $credentials['client_id'];
		$librejo_client = new Client\Client($entity);
    	$endpoint = $librejo_client->oauth_endpoint().'?client_id='.$credentials['client_id'];
    	return $endpoint;
	}
	else {
		$librejo_client = new Client\Client($entity);
		$librejo_app = new App($_GET['entity'], array());
		$app_post = file_get_contents('app.json');
		$create_app = $librejo_app->new_app($entity, $app_post);
		$register = $librejo_app->register();
		$_SESSION['client_id'] = $librejo_app->client_id();
		$_SESSION['hawk_id'] = $librejo_app->hawk_id();
		$_SESSION['hawk_key'] = $librejo_app->hawk_key();
		$_SESSION['entity_old'] = $entity;
		$endpoint = $librejo_client->oauth_endpoint().'?client_id='.$librejo_app->client_id();
		return $endpoint;
	}
}

function get_oauth($code){
	$entity_sub = str_replace("http://", "", $_SESSION['entity_old']);
	$entity_sub = str_replace("https://", "", $entity_sub);
	$_SESSION['entity_sub'] = $entity_sub;
	if (file_exists('logins/'.urlencode($_SESSION['entity_old']).'.json')) {
		$file_content = json_decode(file_get_contents('logins/'.urlencode($_SESSION['entity_old']).'.json'), true);
		if (isset($file_content['access_token'])) {
			$noerror = true;
			$_SESSION['access_token'] = $file_content['access_token'];
           	$_SESSION['hawk_key'] = $file_content['hawk_key'];
			$_SESSION['entity'] = $_SESSION['entity_old'];
		}
		$credentials = array(
                'entity' => $_SESSION['entity_old'], 
                'client_id' => $_SESSION['client_id'],
                'hawk_id' => $_SESSION['hawk_id'],
                'hawk_key' => $_SESSION['hawk_key']
        );
		$_SESSION['credentials'] = $credentials;
		$_SESSION['entity'] = $_SESSION['entity_old'];
		unset($_SESSION['entity_old']);
		return 'Success';
	}
	else {
		$credentials = array(
                'entity' => $_SESSION['entity_old'], 
                'client_id' => $_SESSION['client_id'],
                'hawk_id' => $_SESSION['hawk_id'],
                'hawk_key' => $_SESSION['hawk_key']
        );
		$_SESSION['credentials'] = $credentials;

        $app = new App($_SESSION['entity_old'], $credentials);
        $oauth = $app->oauth($_GET['code']);
        var_export($oauth);
        $_SESSION['hawk_key'] = $oauth['hawk_key'];
        $_SESSION['access_token'] = $oauth['access_token'];
        $_SESSION['hawk_id'] = $oauth['access_token'];

		// Writing the credentials file
		$credentials_file = array('hawk_key' => $oauth['hawk_key'], 'access_token' => $oauth['access_token'], 'client_id' => $_SESSION['client_id']);
		$credentials_file = json_encode($credentials_file,JSON_UNESCAPED_UNICODE);
		$file = fopen('logins/'.urlencode($_SESSION['entity_old']).'.json', 'w') or die("Error opening output file");
		fwrite($file, $credentials_file);
		fclose($file);

		$_SESSION['entity'] = $_SESSION['entity_old'];
		unset($_SESSION['entity_old']);
		return true;
	}
}