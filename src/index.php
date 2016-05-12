<?php

require 'vendor/autoload.php';

$bcs = [
    new Zeus\Barcode\Febraban\Bloqueto('03399221301840006320494101801010367340000043371'),
    new Zeus\Barcode\Febraban\Convenio('846000000014 142910290618 444859012015 603189999999'),
    new Zeus\Barcode\Ean8('55123457'),
    new Zeus\Barcode\Ean13('7501031311309'),
    new Zeus\Barcode\UpcA('042100005264'),
    new Zeus\Barcode\Codabar('A40156B'),
    new Zeus\Barcode\Code11('123-455', false)
];
$bcs[]  = new Zeus\Barcode\UpcE($bcs[4]);

$render = new Zeus\Barcode\Renderer\HtmlRenderer();
$render->setTextPosition(\Zeus\Barcode\Renderer\RendererInterface::TEXT_POSITION_BOTTOM);

foreach ($bcs as &$bc) {
    echo "<p>" . get_class($bc) . "</p>";
    echo $bc->render($render)->getResource() . '<br><br>';
}
