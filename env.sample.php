<?php
declare(strict_types=1);
$variables = [
    'WOLFRAMALPHA_APPID' => 'Get one from https://products.wolframalpha.com/api/',
    'GOOGLE_APPLICATION_CREDENTIALS' => 'The path for the json with the credentials'
];
foreach ($variables as $key => $value) {
    putenv("$key=$value");
}