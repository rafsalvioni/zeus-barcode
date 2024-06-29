<?php
require '../vendor/autoload.php';

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\Code2of5\Standard;
use Zeus\Barcode\Upc\Ean13;
use Zeus\Barcode\Renderer\SvgRenderer;

$renderer = new SvgRenderer();

$bc = new Ean13('938746850912', false);
$bc->scale(5);
$bc->draw($renderer)->render(); // Show the result