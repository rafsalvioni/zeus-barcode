<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399.72101 20500.000110 04833.601018 4 67000000039211'),
    new \Zeus\Barcode\Febraban\Convenio('83600000000-7 83970048100-6 20047123681-6 00100993938-8'),
    new \Zeus\Barcode\Code2of5\Standard('12345670'),
    new \Zeus\Barcode\Code2of5\Interleaved('12345670'),
    new Zeus\Barcode\Codabar('A12345+670C'),
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
    (new Zeus\Barcode\Code93('TEST93a]', false)),
    (new Zeus\Barcode\Postnet('801221905', false)),
    (new Zeus\Barcode\Code128('1235RAF523salvi5ç')),
];

$render = new Zeus\Barcode\Renderer\HtmlRenderer();
$render->setTextPosition(\Zeus\Barcode\Renderer\RendererInterface::TEXT_POSITION_BOTTOM);

//$foo = \Zeus\Barcode\Postnet::fromBinary('1100101100000011001010010100011101001100001010001011');
//$foo = \Zeus\Barcode\Codabar::fromBinary('101100100101010110010101001011011001010101011010010110101001010110011001101001010110100101101010101001101010010011');
//$foo = \Zeus\Barcode\Code11\Code11C::fromBinary('1011001011010110100101101100101010110101011011011011010110110101011001');
//$foo = \Zeus\Barcode\Msi\Msi::fromBinary('1101101001001001001001001001001101001101001001101001001001101101001');
//$foo = \Zeus\Barcode\Msi\MsiMod10::fromBinary('1101101001001001001001001001001101001101001001101001001001101101101001001101001101001001001');
//$foo = \Zeus\Barcode\Code128::fromBinary('1101001110010110011100100010001101011110111011000101110101000110001000110001011011100100110011100101100101110010111100100100101100001100101000011110100100100001101001101110010010111101110100010001101011110111010011000100110010100001100011101011');
$foo = \Zeus\Barcode\Code93::fromBinary('1010111101101001101100100101101011001101001101000010101010000101001100101101010001110110101010011001100100101101100101010111101');
//$foo = \Zeus\Barcode\Upc\Ean13::fromBinary('10101100010100111001100101001110111101011001101010100001011001101100110100001011100101110100101');
//$foo = \Zeus\Barcode\Upc\Ean2::fromBinary('10110100001010100011');
//$foo = \Zeus\Barcode\Upc\Ean5::fromBinary('10110110001010011001010011011010111101010011101');
//$foo = \Zeus\Barcode\Upc\Ean8::fromBinary('1010110001011000100110010010011010101000010101110010011101000100101');
//$foo = \Zeus\Barcode\Upc\Upca::fromBinary('10100011010111011011000101011110111011011011101010110011010100001011100110011011011001001110101');
//$foo = \Zeus\Barcode\Upc\Upce::fromBinary('101001110100100110111001001101101011110011001010101');

foreach ($bcs as &$bc) {
    echo "<p>--------------" . get_class($bc) . "--------------</p>";
    echo "<p>Binary: " . $bc->getEncoded()->getBinary() . "</p>";
    if ($bc instanceof \Zeus\Barcode\ChecksumInterface) {
        echo "<p>Checksum: " . $bc->getChecksum() . "</p>";
    }
    echo $bc->render($render)->getResource() . '<br><br>';
}

