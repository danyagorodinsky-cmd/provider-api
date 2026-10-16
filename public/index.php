<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

function loadTariffs(): array
{
    $json = file_get_contents(__DIR__ . '/../data/tariffs.json');
    return json_decode($json, true);
}

function jsonResponse(Response $response, $data, int $status = 200): Response
{
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus($status);
}

// Усі тарифи (з необов'язковим фільтром ?max_price=300)
$app->get('/api/tariffs', function (Request $request, Response $response) {
    $tariffs = loadTariffs();
    $params = $request->getQueryParams();

    if (isset($params['max_price'])) {
        $tariffs = array_values(array_filter($tariffs, function ($t) use ($params) {
            return $t['price_uah'] <= (int)$params['max_price'];
        }));
    }

    return jsonResponse($response, $tariffs);
});

// Тариф за id
$app->get('/api/tariffs/{id}', function (Request $request, Response $response, array $args) {
    foreach (loadTariffs() as $tariff) {
        if ($tariff['id'] == $args['id']) {
            return jsonResponse($response, $tariff);
        }
    }
    return jsonResponse($response, ['error' => 'Тариф не знайдено'], 404);
});

$app->run();