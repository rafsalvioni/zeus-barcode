<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Codabar;

/**
 * 
 * @author Rafael M. Salvioni
 */
class CodabarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            'A12345B'  => [true, true],
            'C12345D'  => [true, true],
            '123456'   => [true, true],
            'Z456DFV'  => [false, false],
            '2345D'    => [false, false],
            '8-$:\\.'  => [true, false],
            ''         => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Codabar($data, $info[0]);
                $this->assertTrue($info[1]);
            }
            catch (\Exception $ex) {
                $this->assertFalse($info[1]);
            }
        }
    }

    /**
     * @test
     * @depends validationTest
     */
    public function infoTest()
    {
        $bc = new Codabar('A40156B');
        $this->assertEquals($bc->getData(), '40156');
        $this->assertEquals($bc->getEncoded(), '10110010010101101001010101001101010110010110101001010010101101001001011');
    }
}
