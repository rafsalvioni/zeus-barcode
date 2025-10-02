<?php

namespace ZeusTest\Barcode\Code2of5;

use Zeus\Barcode\Code2of5\Interleaved;

/**
 * 
 * @author Rafael M. Salvioni
 */
class Interleaved25Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function validationTest()
    {
        $dataArr = [
            '12345670'  => [true, true],
            '12345674'  => [true, false],
            '012345670' => [true, false],
            '01234567'  => [false, false],
            '1234567'   => [false, true],
            '123456w'   => [false, false],
            '2'         => [false, true]
        ];
        
        foreach ($dataArr as $data => &$info) {
            try {
                new Interleaved($data, $info[0]);
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
        $bc = new Interleaved('12345670');
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getRealData(), '1234567');
        $this->assertEquals($bc->getEncoded()->getBinary(), '101011101000101011100011101110100010100011101000111000101010101000111000111011101');
    }
    
    /**
     * @test
     * @depends validationTest
     */
    public function withoutChecksumTest()
    {
        $bc = new Interleaved('1234567', false);
        $this->assertEquals($bc->getData(), '12345670');
        $this->assertEquals($bc->getChecksum(), '0');
        $this->assertEquals($bc->getRealData(), '1234567');
        $this->assertEquals($bc->getEncoded()->getBinary(), '101011101000101011100011101110100010100011101000111000101010101000111000111011101');
    }
}
