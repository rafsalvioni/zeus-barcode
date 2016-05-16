<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399221301840006320494101801010367340000043371'),
    new Zeus\Barcode\Febraban\Convenio('846000000014 142910290618 444859012015 603189999999'),
    new Zeus\Barcode\Ean8('55123457'),
    new Zeus\Barcode\Ean13('7501031311309'),
    new Zeus\Barcode\UpcA('03600029145', false),
    new Zeus\Barcode\Codabar('40156'),
    new Zeus\Barcode\Industrial25('12345670'),
    new \Zeus\Barcode\Interleaved25('12345670'),
    new Zeus\Barcode\Code11('123-4552'),
    new Zeus\Barcode\Ean2('34'),
    new Zeus\Barcode\Ean5('51234'),
];
$bcs[] = new Zeus\Barcode\UpcE('04252614');

$render = new Zeus\Barcode\Renderer\HtmlRenderer();
$render->setTextPosition(\Zeus\Barcode\Renderer\RendererInterface::TEXT_POSITION_BOTTOM);

foreach ($bcs as &$bc) {
    echo "<p>" . get_class($bc) . "</p>";
    if ($bc instanceof \Zeus\Barcode\ChecksumBarcodeInterface) {
        echo "<p>Checksum: " . $bc->getChecksum() . "</p>";
    }
    echo $bc->render($render)->getResource() . '<br><br>';
}

