<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399.72101 20500.000110 04833.601018 4 67000000039211'),
    new \Zeus\Barcode\Febraban\Convenio('83600000000-7 83970048100-6 20047123681-6 00100993938-8'),
    new \Zeus\Barcode\Code2of5\Standard('12345670'),
    new \Zeus\Barcode\Code2of5\Interleaved('12345670'),
    new Zeus\Barcode\Codabar('A12345670C'),
    new Zeus\Barcode\Upc\Ean13('7501031311309'),
    new Zeus\Barcode\Upc\Ean8('55123457'),
    new Zeus\Barcode\Upc\Ean2('34'),
    new Zeus\Barcode\Upc\Ean5('51234'),
    new Zeus\Barcode\Upc\Upca('075678164125'),
    new Zeus\Barcode\Upc\Upce('04252614'),
    new Zeus\Barcode\Code11\Code11C('123-45', false),
    new Zeus\Barcode\Code11\Code11K('123-45', false),
    (new Zeus\Barcode\Msi\Msi('80523')),
    (new Zeus\Barcode\Msi\Msi2Mod10('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod10('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod11('80523', false)),
    (new Zeus\Barcode\Msi\MsiMod1110('80523', false)),
    (new Zeus\Barcode\Code39\Code39('BARCODE1%')),
    (new Zeus\Barcode\Code39\Code39Mod43('BARCODE1%P')),
    (new Zeus\Barcode\Code39\Code39Ext('BARCODE1%')),
    (new Zeus\Barcode\Code39\Code39ExtMod43('$%&b@', false)),
    (new Zeus\Barcode\Code93('TEST93', false)),
    (new Zeus\Barcode\Postnet('801221905', false)),
    (new Zeus\Barcode\Code128('1525381a', false)),
];

$render = new Zeus\Barcode\Renderer\HtmlRenderer();
$render->setTextPosition(\Zeus\Barcode\Renderer\RendererInterface::TEXT_POSITION_BOTTOM);

foreach ($bcs as &$bc) {
    echo "<p>" . get_class($bc) . "</p>";
    echo "<p>" . $bc->getEncoded()->getBinary() . "</p>";
    if ($bc instanceof \Zeus\Barcode\ChecksumInterface) {
        echo "<p>Checksum: " . $bc->getChecksum() . "</p>";
    }
    echo $bc->render($render)->getResource() . '<br><br>';
}

