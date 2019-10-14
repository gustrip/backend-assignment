<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "localhost";
$config['db']['user']   = "kapadais";
$config['db']['pass']   = "kapadais";
$config['db']['dbname'] = "assignment";

$app = new \Slim\App(['settings'=> $config]);
$container = $app->getContainer();
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->add(\RateLimit\Middleware\RateLimitMiddleware::createDefault(
    \RateLimit\RateLimiterFactory::createRedisBackedRateLimiter([
       'host' => 'localhost',
       'port' => 6379,
   ], 10, 36000)
));

$app->add(new RKA\Middleware\IpAddress());

$app->get('/v1/ships/mmsi', function (Request $request, Response $response) {
	$content_type = Utils::defineContentType($request);
	if(!$content_type){
		$response->getBody()->write("415 Unsupported Media Type");
    	return $newResponse = $response->withStatus(415);
	}

    $queryParams= $request->getQueryParams('mmsi');

	$this->logger->info((string)$request->getUri());

	if(!Utils::correctQueryParams($queryParams,'mmsi')){
		$response->getBody()->write("wrong parameters");
    	return $newResponse = $response->withStatus(400);
    }

    // set parameters properly if there are multiple parameters with the same name 
    $mmsis = Utils::fixMmsiParams($queryParams);
    
    //create array with all ships for every mmsi parameter 
    $ships_mmsi = array();
    foreach ($mmsis as $mmsi) {
    	$ship_mapper = new ShipMapper($this->db);
    	$ships = $ship_mapper->getShipsByMmsi($mmsi);
    	array_push($ships_mmsi, Utils::getCorrectArray($ships)); // helper function
    }
    $message = Utils::getProperMessage($content_type,$ships_mmsi,true);
    
    $response->getBody()->write($message);
    return $newResponse = $response->withStatus(200)->withHeader('Content-type', $content_type);
    
    
});

$app->get('/v1/ships/coordinates', function (Request $request, Response $response) {
    $content_type = Utils::defineContentType($request);
    if(!$content_type){
		$response->getBody()->write("415 Unsupported Media Type");
    	return $newResponse = $response->withStatus(415);
	}

    $queryParams= $request->getQueryParams();
	$this->logger->info((string)$request->getUri());

	if(!Utils::correctQueryParams($queryParams,'coordinates')){
		$response->getBody()->write("wrong parameters");
    	return $newResponse = $response->withStatus(400);
    }
	
	//set properly the coordinates ranges even if the client doesn't provide some
	$finalParams = Utils::fixCoordinatesParams($queryParams);

	$ship_mapper = new ShipMapper($this->db);
	$ships = $ship_mapper->getShipsByCoordinates($finalParams);
	
	$ships_coord = Utils::getCorrectArray($ships);

	$message = Utils::getProperMessage($content_type,$ships_coord);

    $response->getBody()->write($message);
    return $newResponse = $response->withStatus(200)->withHeader('Content-type', $content_type);
    
    
});


$app->get('/v1/ships/time-interval', function (Request $request, Response $response) {
    $content_type = Utils::defineContentType($request);
    if(!$content_type){
		$response->getBody()->write("415 Unsupported Media Type");
    	return $newResponse = $response->withStatus(415);
	}
    $queryParams= $request->getQueryParams();
	$this->logger->info((string)$request->getUri());

	if(Utils::correctQueryParams($queryParams,'time-interval')){
		$response->getBody()->write("wrong parameters");
    	return $newResponse = $response->withStatus(400);
    }
	
	//set properly the time ranges even if the client doesn't provide some
	$finalParams = Utils::fixDateIntervalParams($queryParams);

	$ship_mapper = new ShipMapper($this->db);
	$ships = $ship_mapper->getShipsByDateInterval($finalParams);

	$ships_interval = Utils::getCorrectArray($ships);

	$message = Utils::getProperMessage($content_type,$ships_interval);

    $response->getBody()->write($message);
    return $newResponse = $response->withStatus(200)->withHeader('Content-type', $content_type);
    
    
});



$app->run();
