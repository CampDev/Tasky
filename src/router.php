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

$app->get('/auth/{entity}', function($entity) use ($app) {
	return 'Lets authenticate you...';
});