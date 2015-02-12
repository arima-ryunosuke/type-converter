#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ryunosuke\TypeConverter\Mediator;

$mediator = new Mediator('xml', 'json');
$xmlstring = file_get_contents(__DIR__ . '/demo.xml');
echo 'xml -> json:', PHP_EOL, $xmlstring, $mediator->convert($xmlstring), PHP_EOL;

$mediator = new Mediator('json', 'xml');
$jsonstring = file_get_contents(__DIR__ . '/demo.json');
echo 'json -> xml:', PHP_EOL, $jsonstring, $mediator->convert($jsonstring), PHP_EOL;
