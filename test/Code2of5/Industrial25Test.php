<?php

namespace ZeusTest\Barcode\Code2of5;

use Zeus\Barcode\Code2of5\Standard;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Industrial25Test extends \PHPUnit\Framework\TestCase
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
                new Standard($data, $info[0]);
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
        $bc = new Standard('12345670');
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getRealData(), '1234567');
        $this->assertEquals($bc->getEncoded()->getBinary(), '11101110101110101010111010111010101110111011101010101010111010111011101011101010101110111010101010101110111010101110111010111010111');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Standard('1234567', false);
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getRealData(), '1234567');
        $this->assertEquals($bc->getEncoded()->getBinary(), '11101110101110101010111010111010101110111011101010101010111010111011101011101010101110111010101010101110111010101110111010111010111');
    }
}
