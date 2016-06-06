<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399.72101 20500.000110 04833.601018 4 67000000039211'),
    new \Zeus\Barcode\Febraban\Convenio('836000000007 839700481006 200471236816 001009939388'),
    new \Zeus\Barcode\Code2of5\Standard('12345670'),
    new \Zeus\Barcode\Code2of5\Interleaved('098765789', false),
    new Zeus\Barcode\Codabar('A12345670C'),
    new Zeus\Barcode\Upc\Ean13('978098754612', false),
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
    (new Zeus\Barcode\Code39\Code39('ZEUS-FRAMEWORK')),
    (new Zeus\Barcode\Code39\Code39Mod43('BARCODE1%P')),
    (new Zeus\Barcode\Code39\Code39Ext('BARCODE1%')),
    (new Zeus\Barcode\Code39\Code39ExtMod43('$%&b@', false)),
    (new Zeus\Barcode\Code93('TEST93', false)),
    (new Zeus\Barcode\Postnet('801221905', false)),
    (new Zeus\Barcode\Code128("Eça de Queirós")),
    (new Zeus\Barcode\Ean128('010123456789012815051231')),
    (new Zeus\Barcode\Itf14('1234567890123', false)),
    \Zeus\Barcode\Upc\ISBN::fromISBN('9876543789'),
    \Zeus\Barcode\Upc\ISSN::fromISSN('378-5955'),
    new \Zeus\Barcode\DHL\Leitcode('21348075016401'),
    new \Zeus\Barcode\DHL\Identcode('563102430313'),
];

//$render = new Zeus\Barcode\Renderer\ImageRenderer();
//$gd = \imagecreatefrompng('D:\\Users\\rafaelsalvioni\\Desktop\\01.png');
//$gd = \imagecreatetruecolor(1000, 4000);
//$render->setResource('C:\\Users\\Rafael\\Desktop\\boleto.jpg');
//$render = new \Zeus\Barcode\Renderer\SvgRenderer();
//$render->setResource('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="4000" width="600"></svg>');
$render = new Zeus\Barcode\Renderer\FpdfRenderer();
$render->merge = true;
//$render->offsetLeft = 120;
//$render->offsetTop = 20;

foreach ($bcs as &$bc) {
    //$bc->textAlign = 'right';
    //$bc->textPosition = 'top';
    //$bc->border = 5;
    //$bc->barwidth = 2;
    //$bc->barheight = 80;
    //$bc->quietZone = 20;
    //$bc->font = 'D:\\Users\\rafaelsalvioni\\Desktop\\arial.ttf';
    $bc->fontSize = 7;
    //$bc->backColor = 0xffff00;
    //$bc->foreColor = 0xff0000;
    $bc->showText = true;
    //$render->backColor = \rand(0xff0000, 0xffff99);
    //$bc->backColor = \rand(0xff0000, 0xffff99);
    $bc->draw($render);
    //$render->stream($bc);
    //$render->offsetLeft += $bc->getTotalWidth() + 20;
    //$render->drawText([0, 0], \get_class($bc), 0xffff00, $bc->font, $bc->fontSize + 2);
    $render->offsetTop += $bc->getTotalHeight() + 50;
}
$render->render();

/*$render->stream(\array_last($bcs))
       ->stream(new \Zeus\Barcode\Upc\Ean5('87624'))
       ->stream(new \Zeus\Barcode\Upc\Ean2('84'))
       ->render();*/
