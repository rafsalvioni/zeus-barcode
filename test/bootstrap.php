<?php
require __DIR__ . '/../vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Code2of5\Standard;
use Zeus\Barcode\Upc\Ean13;
use Zeus\Barcode\Renderer\SvgRenderer;

$renderer = new SvgRenderer();

$bc = new \Zeus\Barcode\Upc\ISBN('978746850912', false);
$bc->scale(2);
$bc->draw($renderer)->render(); // Show the result