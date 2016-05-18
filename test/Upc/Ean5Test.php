<?php

namespace ZeusTest\Barcode\Upc;

use Zeus\Barcode\Upc\Ean5;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Ean5Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '51234' => [true, true, '0011101'],
            '98765' => [true, true, '0111001'],
            '091'   => [true, true, '0011001'],
            ''      => [true, true, '0001101'],
            'x'     => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                $bc = new Ean5($data, $info[0]);
                $this->assertTrue($info[1]);
                $this->assertStringEndsWith($info[2], $bc->getEncoded());
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }
}
