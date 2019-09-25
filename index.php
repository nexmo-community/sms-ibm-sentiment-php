<?php

require('vendor/autoload.php');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

$app = AppFactory::create();

// load .env content to $_ENV
Dotenv\Dotenv::create(__DIR__)->load();

// allow any request, return error on non-POST
$app->any('/message[/]', function (Request $request, Response $response, array $args) {

    if ($request->getMethod() != 'POST') {
        return $response->withStatus(405);
    }

    $client = new Client();

    $body = json_decode($request->getBody());

    // Make request to Watson service
    $result = $client->request(
        'GET',
        $_ENV['TONE_ANALYZER_URL'] . '?version=2017-09-21&text=' . urlencode($body->text),
        ['auth' => ['apikey', $_ENV['TONE_ANALYZER_IAM_APIKEY']]]
    );

    // Retrieve Content-Type, or define it
    $contentType = $result->getHeaderLine('Content-Type') ?: 'application/json';

    $response = $response->withHeader('Content-Type', $contentType);

    return $response->withBody($result->getBody());
});

$app->run();
