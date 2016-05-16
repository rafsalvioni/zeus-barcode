<?php

namespace ZeusTest\Barcode;

use Zeus\Barcode\Industrial25;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Industrial25Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '12345670' => [true, true],
            '12345674' => [true, false],
            '1234567'  => [false, true],
            '123456w'  => [false, false],
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Industrial25($data, $info[0]);
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
    public function withChecksumTest()
    {
        $bc = new Industrial25('12345670');
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getDataWithoutChecksum(), '1234567');
        $this->assertEquals($bc->getEncoded(), '1101101011101010101110101110101011101110111010101010101110101110111010111010101011101110101010101011101110101011101110101101011');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Industrial25('1234567', false);
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getDataWithoutChecksum(), '1234567');
        $this->assertEquals($bc->getEncoded(), '1101101011101010101110101110101011101110111010101010101110101110111010111010101011101110101010101011101110101011101110101101011');
    }
}
