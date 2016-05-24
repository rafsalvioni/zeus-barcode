<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Ean128;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Ean128Test extends Code128Test
{
    /**
     * @test
     * @depends validateTest
     */
    public function fnc1Test()
    {
        for ($i = 0; $i < 10; $i++) {
            $string = \uniqid();
            $obj = new Ean128($string);
            $this->assertEquals(\substr($obj->getEncoded()->getBinary(), 11, 11), '11101011110');
        }
    }
}
