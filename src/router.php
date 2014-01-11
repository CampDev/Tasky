<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/', function() use ($app) {
	if (isset($_SESSION['entity'])) {
		return $app['twig']->render('index.twig', array(
		'authed_user' => $_SESSION['entity']
	));	
	}
	else {
		return $app['twig']->render('landing.twig', array());
	}
})->bind('index');

// Authentication
$app->get('/auth/', function() use ($app) {
	if (substr(urldecode($_GET['entity']), -1) == '/') {
    	$entity = urldecode(substr($_GET['entity'], 0, -1));
    }
    else {
        $entity = urldecode($_GET['entity']);
    }
	$oauth_url = register_app($entity);
	return $app->redirect($oauth_url);
});

$app->get('/redirect/', function() use ($app) {
	$auth = get_oauth($_GET['code']);
	if ($auth == true) {
		return $app->redirect('../');	
	}
	else {
		echo $code;
		return '';
	}
});

if($app['debug'] == true){
	$app->get('/session/', function() {
		var_export($_SESSION);
		return '';
	});
}