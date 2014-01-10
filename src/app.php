<?php

$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

$app->register(new Silex\Provider\TwigServiceProvider, array(
    'twig.path' => array(
        __DIR__.'/../views/',
    ),
));

$app['twig'] = $app->share($app->extend('twig', function ($twig, $c) {
	return $twig;
}));

$app['deubg'] = true;