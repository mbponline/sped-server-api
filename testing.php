<?php

required __DIR__.'/vendor/autoload.php';

$client = new \Guzzle\Http\Client([
        'base_url' = 'http://localhost/',
        'defaults' => [
             'exeception' => false,
        ]
]);


$response = $client->post('');

echo $response;



?>