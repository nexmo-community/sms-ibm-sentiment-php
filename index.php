<?php

require('vendor/autoload.php');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

$app = AppFactory::create();

// load .env content for API credentials
Dotenv\Dotenv::create(__DIR__)->load();

// route allows any GET or POST request
$app->any('/message/[{content}]', function (Request $request, Response $response, array $args) {

    $method = $request->getMethod();
    $client = new Client();

    $url = $_ENV['TONE_ANALYZER_URL'] . '/v3/tone?version=2017-09-21&text=';

    // is this a POST or GET, handle accordingly otherwise not allowed
    if ($method == "POST") {
        $body = $request->getParsedBody();

        $url .= urlencode($body['content']);
    } elseif ($method == "GET") {
        $url .= urlencode($args['content']);
    } else {
        return $response->withStatus(405);
    }

    $result = $client->request('GET', $url, ['auth' => ['apikey', $_ENV['TONE_ANALYZER_IAM_APIKEY']]]);

    $data = json_decode($result->getBody());

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
