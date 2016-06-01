<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399.72101 20500.000110 04833.601018 4 67000000039211'),
    new \Zeus\Barcode\Febraban\Convenio('836000000007 839700481006 200471236816 001009939388'),
    new \Zeus\Barcode\Code2of5\Standard('12345670'),
    new \Zeus\Barcode\Code2of5\Interleaved('098765789', false),
    new Zeus\Barcode\Codabar('A12345670C'),
    new Zeus\Barcode\Upc\Ean13('7501031311309'),
    new Zeus\Barcode\Upc\Ean8('55123457'),
    new Zeus\Barcode\Upc\Ean2('34'),
    new Zeus\Barcode\Upc\Ean5('51234'),
    new Zeus\Barcode\Upc\Upca('000325235231'),
    new Zeus\Barcode\Upc\Upce('04252614'),
    new Zeus\Barcode\Code11\Code11C('123-45', false),
    new Zeus\Barcode\Code11\Code11K('123-45', false),
    (new Zeus\Barcode\Msi\Msi('80523')),
    (new Zeus\Barcode\Msi\Msi2Mod10('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod10('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod11('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod1110('80523', false)),
    (new Zeus\Barcode\Code39\Code39('ZEND-FRAMEWORK')),
    (new Zeus\Barcode\Code39\Code39Mod43('BARCODE1%P')),
    (new Zeus\Barcode\Code39\Code39Ext('BARCODE1%')),
    (new Zeus\Barcode\Code39\Code39ExtMod43('$%&b@', false)),
    (new Zeus\Barcode\Code93('TEST93', false)),
    (new Zeus\Barcode\Postnet('801221905', false)),
    (new Zeus\Barcode\Code128("Eça de Queirós")),
    (new Zeus\Barcode\Ean128('010123456789012815051231')),
    (new Zeus\Barcode\Itf14('1234567890123', false)),
];

$render = new Zeus\Barcode\Renderer\ImageRenderer();
//$gd = \imagecreatefrompng('D:\\Users\\rafaelsalvioni\\Desktop\\01.png');
$gd = \imagecreatetruecolor(600, 4000);
$render->setResource($gd);
//$render = new \Zeus\Barcode\Renderer\SvgRenderer();
//$render->setResource('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="4000" width="600" fill="#ffff00"></svg>');
$render->offsetLeft = 70;
$render->offsetTop = 10;

foreach ($bcs as &$bc) {
    //$bc->fontSize = 3;
    //$bc->textAlign = 'left';
    //$bc->textPosition = 'top';
    //$bc->border = 2;
    //$bc->barwidth = 2;
    $bc->barheight = 50;
    $bc->quietZone = 20;
    $bc->font = 'D:\\Users\\rafaelsalvioni\\Desktop\\arial.ttf';
    $bc->fontSize = 8;
    //$bc->backColor = 0xffff00;
    //$bc->foreColor = 0xff0000;
    $bc->showText = true;
    $bc->draw($render);
    $render->offsetTop += $bc->getHeight() + 20;
    //$render->drawText([0, 0], \get_class($bc), 0xffff00, $bc->font, $bc->fontSize + 2);
    //$render->offsetTop += 50;
}
$render->render();
