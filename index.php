<?php
/**
* @author  : Yudi Purwanto <yp@timexstudio.com or purwantoyudi42@gmail.com>
* @link    : https://timexstudio.com (jangan meminta menjadi sempurna, tapi jadilah yang berguna)
* @since   : 28 Oct 2015
**/
require 'vendor/autoload.php';

// Prepare app
$app = new \Slim\Slim(array(
	'debug' => getenv("DEBUG"),
	'mode' => getenv("MODE"),
    'templates.path' => 'resources/views'
));

// Add Middleware
// $app->add(new \Slim\Middleware\JwtAuthentication([
	 // "path" => ["/provinsi", "/city", "/cost"],
     // "secret" => getenv("JWT_AUTH"),
	 // "logger" => $monolog,
	 // "callback" => function ($options) use ($app) {
        // $app->jwt = $options["decoded"];
    // }
// ]));

// Using Twig
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('resources/views');
$twig = new Twig_Environment($loader, array(
    'cache' => 'resources/views',
	'debug' => true
));
// $template = $twig->loadTemplate('index.html');
// $template->render();

// Prepare view
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);

$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

$rajaongkir = new hok00age\RajaOngkir(getenv("KEY_RAJAONGKIR"));
$app->response->headers->set('Content-Type', 'application/json');

// Define routes
$app->get('/', function () use ($app) {
    // Render index view
    $app->render('index.html');
});

$app->get('/provinsi', function() use ($app, $rajaongkir) {
	try {	
		$provinsi = $rajaongkir->getProvince();
		$response = $provinsi->raw_body;
	} catch(\Exception $e) {
		$provinsi = '{"error":{"text":'. $e->getMessage() .'}}'; 
		$response = $provinsi;
	}
	echo $response;
});

$app->get('/city', function() use ($app, $rajaongkir) {
	try {	
		$city = $rajaongkir->getCity();
		$response = $city->raw_body;
	} catch(\Exception $e) {
		$city = '{"error":{"text":'. $e->getMessage() .'}}'; 
		$response = $city;
	}
	echo $response;
});

$app->get('/cost', function() use ($app, $rajaongkir) {
	$get = [
			'origin' => $app->request()->post('origin') ? $app->request()->post('origin') : 501,
			'destination' => $app->request()->post('destination') ? $app->request()->post('destination') : 114,
			'weight' => $app->request()->post('weight') ? $app->request()->post('weight') : 1700,
			'courier' => $app->request()->post('courier') ? $app->request()->post('courier') : 'jne',
		];
	try {	
		$cost = $rajaongkir->getCost($get['origin'], $get['destination'], $get['weight'], $get['courier']);
		$response = $cost->raw_body;
	} catch(\Exception $e) {
		$cost = '{"error":{"text":'. $e->getMessage() .'}}';
		$response = $cost;
	}
	echo $response;
});

// Run app
$app->run();
