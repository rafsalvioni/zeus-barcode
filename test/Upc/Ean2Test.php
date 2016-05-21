<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\Ean2;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Ean2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '34' => [true, true, '0100011'],
            '97' => [true, true, '0010001'],
            '1'  => [true, true, '0110011'],
            ''   => [true, false, '0001101'],
            'x'  => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new Ean2($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertStringEndsWith($info[2], $bc->getEncoded()->getBinary());
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
